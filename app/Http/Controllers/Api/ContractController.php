<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractStoreRequest;
use App\Http\Resources\ContractResource;
use App\Models\AutoMobileUnit;
use App\Models\Contract;
use App\Models\PropertyUnit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContractController extends Controller
{
    public function index()
    {
        $contract = Contract::orderBy('id', 'asc')->get();

        return ContractResource::collection($contract);
    }


    public function datatables(Request $request)
    {
        $query = Contract::with('contact')->orderBy('created_at', 'desc');

        return DataTables::of($query)
            // ->orderColumn('created_at', function ($query, $order) {
            //     $query->orderBy('created_at', $order);
            // })
            ->addColumn('contract_type', function (Contract $c) {
                return $c->contractType->name;
            })
            ->addColumn('contact', function (Contract $c) {
                return $c->contact->display_name;
            })
            ->filter(function ($query) use ($request) {
                if ($request->has('contract_type') && $request->contract_type != '') {
                    $query->where('contract_type_id', $request->contract_type);
                }

                $searchValue = $request->get('search')['value'] ?? null;
                if ($searchValue) {
                    $query->where(function($q) use ($searchValue) {
                        $q->where('number', 'like', "%{$searchValue}%")
                          ->orWhere('policy_number', 'like', "%{$searchValue}%")
                          ->orWhereHas('contact', function ($query) use ($searchValue) {
                              $query->where('display_name', 'like', "%{$searchValue}%");
                          });
                    });
                }
            })
            ->make(true);
    }

    public function select2(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $query = Contract::with(['contact'])
            ->where('status', 'active')
            ->where('approval_status', 'approved')
            ->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('number', 'like', "%{$search}%")
                      ->orWhere('policy_number', 'like', "%{$search}%")
                      ->orWhereHas('contact', function($contactQuery) use ($search) {
                          $contactQuery->where('display_name', 'like', "%{$search}%");
                      });
                }
            })
            ->orderBy('created_at', 'desc');

        $total = $query->count();
        $contracts = $query->offset($offset)->limit($limit)->get();

        $data = $contracts->map(function ($contract) {
            return [
                'id' => $contract->id,
                'text' => "{$contract->number} - {$contract->contact->display_name}"
            ];
        });

        return response()->json([
            'data' => $data,
            'pagination' => [
                'more' => ($offset + $limit) < $total
            ]
        ]);
    }

    public function show($id)
    {
        $contract = Contract::with([
            'contact', 
            'contractType', 
            'details.insurance', 
            'endorsements.contractReference:id,number,contact_id', 
            'currency'
        ])->findOrFail($id);

        return response()->json([
            'data' => new ContractResource($contract)
        ]);
    }

    public function generateNumber(Request $request)
    {
        try {
            $contractTypeId = $request->get('contract_type_id');
            
            if (!$contractTypeId) {
                return response()->json([
                    'message' => 'Contract Type ID is required'
                ], 400);
            }

            // Get contract type
            $contractType = \App\Models\ContractType::findOrFail($contractTypeId);
            
            // Get contract type code (first 3 letters of name, uppercase, remove non-alpha)
            $contractTypeCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $contractType->name), 0, 3));
            
            // Current month and year
            $month = date('m');
            $year = date('Y');
            
            // Get last contract number for this month and contract type
            $prefix = "{$contractTypeCode}/{$month}/{$year}/";
            $lastContract = Contract::where('number', 'like', "{$prefix}%")
                ->orderBy('number', 'desc')
                ->first();
            
            // Generate running number
            if ($lastContract) {
                // Extract last number
                $lastNumber = (int) substr($lastContract->number, strrpos($lastContract->number, '/') + 1);
                $runningNumber = $lastNumber + 1;
            } else {
                $runningNumber = 1;
            }
            
            // Format: KODE/MM/YYYY/00001
            $contractNumber = sprintf("%s%05d", $prefix, $runningNumber);
            
            return response()->json([
                'data' => [
                    'number' => $contractNumber
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(ContractStoreRequest $request)
    {
        try {
            $data = $request->validated();
            
            $contract = Contract::create([
                'contract_status' => $data['contract_status'],
                'contract_type_id' => $data['contract_type_id'],
                'number' => $data['number'],
                'policy_number' => $data['policy_number'] ?? null,
                'policy_fee' => $data['policy_fee'] ?? null,
                'contact_id' => $data['contact_id'],
                'period_start' => $data['period_start'],
                'period_end' => $data['period_end'],
                'currency_code' => $data['currency_code'],
                'exchange_rate' => $data['exchange_rate'],
                'coverage_amount' => $data['coverage_amount'],
                'gross_premium' => $data['gross_premium'],
                'discount' => $data['discount'],
                'stamp_fee' => $data['stamp_fee'],
                'amount' => $data['amount'],
                'installment_count' => $data['installment_count'],
                'memo' => $data['memo'],
                'status' => 'active',
                'covered_item' => $data['covered_item'],
                'approval_status' => 'pending',
                // 'created_by' => auth()->id()
            ]);

            // check if details is not empty
            if (!empty($data['details'])) {
                foreach ($data['details'] as $detail) {
                    $contract->details()->create([
                        'insurance_id' => $detail['insurance_id'],
                        'description' => $detail['description'],
                        'percentage' => $detail['percentage'],
                        'brokerage_fee' => $detail['brokerage_fee'],
                        'eng_fee' => $detail['eng_fee'],
                    ]);
                }
            }

            // Save endorsements
            if (!empty($data['endorsements'])) {
                foreach ($data['endorsements'] as $endorsement) {
                    // Only save if has data
                    if (!empty($endorsement['contract_reference_id']) || !empty($endorsement['endorsement_number'])) {
                        $contract->endorsements()->create([
                            'contract_reference_id' => $endorsement['contract_reference_id'] ?? null,
                            'endorsement_number' => $endorsement['endorsement_number'] ?? null,
                        ]);
                    }
                }
            }

            return response()->json([
                'message' => 'Data has been created',
                'data' => new ContractResource($contract)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function storeAutomobileUnit(Request $request, Contract $contract)
    {
        $request->validate([
            'units' => 'required|array',
            'units.*.no_polisi' => 'required|string',
            'units.*.merk' => 'required|string',
            'units.*.tahun' => 'required|string',
            'units.*.no_rangka' => 'required|string',
            'units.*.no_mesin' => 'required|string',
            'units.*.penggunaan' => 'required|string',
            'units.*.total' => 'required|numeric',
            'units.*.valuta' => 'nullable|string',
            'units.*.cover' => 'nullable|string',
            'units.*.discount' => 'nullable|numeric',
            'units.*.rate' => 'nullable|numeric',
            'units.*.brokerage' => 'nullable|numeric',
        ]);

        foreach ($request->units as $unit) {
            AutoMobileUnit::create([
                'contract_id' => $contract->id,
                'nopolisi' => $unit['no_polisi'],
                'merk' => $unit['merk'],
                'tahun' => $unit['tahun'],
                'norangka' => $unit['no_rangka'],
                'nomesin' => $unit['no_mesin'],
                'penggunaan' => $unit['penggunaan'],
                'valuta' => $unit['valuta'] ?? 'IDR',
                'total' => $unit['total'],
                'idcover' => $unit['cover'] ?? null,
                'discount' => $unit['discount'] ?? null,
                'rate' => $unit['rate'] ?? null,
                'brokerage' => $unit['brokerage'] ?? null,
            ]);
        }

        return response()->json(['message' => 'Automobile units successfully saved.']);
    }

    public function storePropertyUnit(Request $request, Contract $contract)
    {
        $request->validate([
            'units' => 'required|array',
            'units.*.location' => 'required|string',
            'units.*.risk_type' => 'required|string',
            'units.*.reinstallment_value_clause' => 'string',
            'units.*.nominated_loss_adjuster' => 'string',
            'units.*.discount' => 'required|string',
        ]);

        foreach ($request->units as $unit) {
            PropertyUnit::create([
                'contract_id' => $contract->id,
                'location' => $unit['location'],
                'risk_type' => $unit['risk_type'],
                'reinstallment_value_clause' => $unit['reinstallment_value_clause'] ?? 0,
                'nominated_loss_adjuster' => $unit['nominated_loss_adjuster'] ?? 0,
                'discount' => $unit['discount'] ?? 0,
            ]);
        }

        return response()->json(['message' => 'Property units successfully saved.']);
    }

    public function update(ContractStoreRequest $request, $id)
    {
        try {
            $contract = Contract::findOrFail($id);

            // Check if user is admin
            if (auth()->user()->role !== 'admin') {
                return response()->json([
                    'message' => 'Unauthorized. Only admins can update contracts.'
                ], 403);
            }

            // Check if contract is pending or rejected
            if (!in_array($contract->approval_status, ['pending', 'rejected'])) {
                return response()->json([
                    'message' => 'Cannot update approved contracts.'
                ], 403);
            }

            $data = $request->validated();
            
            $contract->update([
                'contract_status' => $data['contract_status'],
                'contract_type_id' => $data['contract_type_id'],
                'number' => $data['number'],
                'policy_number' => $data['policy_number'] ?? null,
                'policy_fee' => $data['policy_fee'] ?? null,
                'contact_id' => $data['contact_id'],
                'period_start' => $data['period_start'],
                'period_end' => $data['period_end'],
                'currency_code' => $data['currency_code'],
                'exchange_rate' => $data['exchange_rate'],
                'coverage_amount' => $data['coverage_amount'],
                'gross_premium' => $data['gross_premium'],
                'discount' => $data['discount'],
                'stamp_fee' => $data['stamp_fee'],
                'amount' => $data['amount'],
                'installment_count' => $data['installment_count'],
                'memo' => $data['memo'],
                'covered_item' => $data['covered_item'],
                'approval_status' => 'pending', // Reset to pending when updated
            ]);

            // Delete existing details and recreate
            $contract->details()->delete();
            
            if (!empty($data['details'])) {
                foreach ($data['details'] as $detail) {
                    $contract->details()->create([
                        'insurance_id' => $detail['insurance_id'],
                        'description' => $detail['description'],
                        'percentage' => $detail['percentage'],
                        'brokerage_fee' => $detail['brokerage_fee'],
                        'eng_fee' => $detail['eng_fee'],
                    ]);
                }
            }

            // Delete existing endorsements and recreate
            $contract->endorsements()->delete();
            
            if (!empty($data['endorsements'])) {
                foreach ($data['endorsements'] as $endorsement) {
                    // Only save if has data
                    if (!empty($endorsement['contract_reference_id']) || !empty($endorsement['endorsement_number'])) {
                        $contract->endorsements()->create([
                            'contract_reference_id' => $endorsement['contract_reference_id'] ?? null,
                            'endorsement_number' => $endorsement['endorsement_number'] ?? null,
                        ]);
                    }
                }
            }

            return response()->json([
                'message' => 'Contract updated successfully',
                'data' => new ContractResource($contract)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            $contract = Contract::findOrFail($id);

            // Check if user is approver
            if (auth()->user()->role !== 'approver') {
                return response()->json([
                    'message' => 'Unauthorized. Only approvers can approve contracts.'
                ], 403);
            }

            // Check if already approved
            if ($contract->approval_status === 'approved') {
                return response()->json([
                    'message' => 'Contract already approved.'
                ], 400);
            }

            $contract->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            return response()->json([
                'message' => 'Contract approved successfully.',
                'data' => new ContractResource($contract)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:1000'
            ]);

            $contract = Contract::findOrFail($id);

            // Check if user is approver
            if (auth()->user()->role !== 'approver') {
                return response()->json([
                    'message' => 'Unauthorized. Only approvers can reject contracts.'
                ], 403);
            }

            // Check if already approved
            if ($contract->approval_status === 'approved') {
                return response()->json([
                    'message' => 'Cannot reject an already approved contract.'
                ], 400);
            }

            $contract->update([
                'approval_status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            return response()->json([
                'message' => 'Contract rejected successfully.',
                'data' => new ContractResource($contract)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadDocument(Request $request, $id)
    {
        try {
            \Log::info("Upload document started for contract ID: $id");
            \Log::info("Request has files: " . ($request->hasFile('documents') ? 'yes' : 'no'));
            
            $contract = Contract::findOrFail($id);
            \Log::info("Contract found: " . $contract->number);

            $request->validate([
                'documents' => 'required|array',
                'documents.*' => 'file|mimes:pdf,xlsx,xls,doc,docx,ppt,pptx,txt,jpg,jpeg,png|max:10240'
            ]);

            \Log::info("Validation passed");

            $uploadedDocuments = [];
            $documents = $contract->documents ?? [];
            \Log::info("Existing documents count: " . count($documents));
            
            foreach ($request->file('documents') as $index => $file) {
                \Log::info("Processing file $index: " . $file->getClientOriginalName());
                
                $originalFilename = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $mimeType = $file->getMimeType();
                $fileSize = $file->getSize();
                
                // Generate unique filename
                $filename = $contract->id . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
                \Log::info("Generated filename: $filename");
                
                // Store file in storage/app/private/documents/contracts/
                $filePath = $file->storeAs(
                    'documents/contracts',
                    $filename,
                    'local'
                );
                \Log::info("File stored at: $filePath");
                
                $docData = [
                    'id' => uniqid(),
                    'filename' => $filename,
                    'original_filename' => $originalFilename,
                    'file_path' => $filePath,
                    'mime_type' => $mimeType,
                    'file_size' => $fileSize,
                    'file_extension' => $fileExtension,
                    'created_at' => now()->toDateTimeString(),
                ];
                
                $documents[] = $docData;
                
                $uploadedDocuments[] = [
                    'id' => $docData['id'],
                    'filename' => $docData['original_filename'],
                    'file_size_formatted' => $this->formatFileSize($fileSize),
                    'uploaded_at' => $docData['created_at'],
                ];
            }

            // Save to database
            \Log::info("Saving documents to database. Total documents: " . count($documents));
            $contract->documents = $documents;
            $savedResult = $contract->save();
            \Log::info("Save result: " . ($savedResult ? 'success' : 'failed'));
            
            // Verify save
            $contract->refresh();
            \Log::info("Documents after refresh: " . count($contract->documents ?? []));

            return response()->json([
                'message' => 'Documents uploaded successfully',
                'data' => $uploadedDocuments,
                'total_documents' => count($documents)
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Upload document error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'Failed to upload documents: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteDocument($contractId, $documentId)
    {
        try {
            $contract = Contract::findOrFail($contractId);
            $documents = $contract->documents ?? [];
            
            $documentToDelete = null;
            $documents = array_filter($documents, function($doc) use ($documentId, &$documentToDelete) {
                if ($doc['id'] === $documentId) {
                    $documentToDelete = $doc;
                    return false;
                }
                return true;
            });

            if (!$documentToDelete) {
                return response()->json([
                    'message' => 'Document not found'
                ], 404);
            }

            // Delete file from storage
            \Storage::disk('local')->delete($documentToDelete['file_path']);
            
            // Update contract with remaining documents
            $contract->documents = array_values($documents);
            $contract->save();

            return response()->json([
                'message' => 'Document deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete document error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete document: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadDocument($contractId, $documentId)
    {
        try {
            $contract = Contract::findOrFail($contractId);
            $documents = $contract->documents ?? [];
            
            $document = null;
            foreach ($documents as $doc) {
                if ($doc['id'] === $documentId) {
                    $document = $doc;
                    break;
                }
            }

            if (!$document) {
                return response()->json([
                    'message' => 'Document not found'
                ], 404);
            }

            $filePath = storage_path('app/' . $document['file_path']);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'message' => 'File not found'
                ], 404);
            }

            return response()->download($filePath, $document['original_filename']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDocuments($id)
    {
        try {
            $contract = Contract::findOrFail($id);
            $documents = $contract->documents ?? [];
            
            $formattedDocuments = array_map(function($doc) {
                return [
                    'id' => $doc['id'],
                    'filename' => $doc['original_filename'],
                    'file_extension' => $doc['file_extension'],
                    'file_size_formatted' => $this->formatFileSize($doc['file_size']),
                    'uploaded_at' => \Carbon\Carbon::parse($doc['created_at'])->format('d M Y H:i'),
                ];
            }, $documents);

            return response()->json([
                'data' => $formattedDocuments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
