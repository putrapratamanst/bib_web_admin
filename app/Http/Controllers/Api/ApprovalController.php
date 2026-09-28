<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Services\ContractApprovalService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApprovalController extends Controller
{
    public function submit(Request $request, Contract $contract, ContractApprovalService $service)
    {
        $data = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'reject'])],
            'reason' => ['required_if:decision,reject', 'nullable', 'string', 'max:1000'],
        ]);

        try {
            $contract = $service->submit(
                $contract,
                $data['decision'],
                (int) $request->user()->id,
                $data['reason'] ?? null,
            );

            return response()->json([
                'message' => 'Approval berhasil diproses.',
                'data' => ['id' => $contract->id, 'approval_status' => $contract->approval_status],
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => $exception->getMessage() ?: 'Approval gagal diproses.',
            ], 422);
        }
    }
}
