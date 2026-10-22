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
        return $this->process($request, fn ($decision, $userId, $reason) => $service->submit(
            $contract,
            $decision,
            $userId,
            $reason,
        ));
    }

    public function submitDebitNote(Request $request, string $id, ContractApprovalService $service)
    {
        return $this->process($request, fn ($decision, $userId, $reason) => $service->submitDebitNote($id, $decision, $userId, $reason));
    }

    public function submitBilling(Request $request, string $id, ContractApprovalService $service)
    {
        return $this->process($request, fn ($decision, $userId, $reason) => $service->submitBilling($id, $decision, $userId, $reason));
    }

    public function submitCreditNote(Request $request, string $id, ContractApprovalService $service)
    {
        return $this->process($request, fn ($decision, $userId, $reason) => $service->submitCreditNote($id, $decision, $userId, $reason));
    }

    private function process(Request $request, \Closure $callback)
    {
        $data = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'reject'])],
            'reason' => ['required_if:decision,reject', 'nullable', 'string', 'max:1000'],
        ]);

        try {
            $model = $callback($data['decision'], (int) $request->user()->id, $data['reason'] ?? null);

            return response()->json([
                'message' => 'Approval berhasil diproses.',
                'data' => ['id' => $model->id, 'approval_status' => $model->approval_status],
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => $exception->getMessage() ?: 'Approval gagal diproses.',
            ], 422);
        }
    }
}
