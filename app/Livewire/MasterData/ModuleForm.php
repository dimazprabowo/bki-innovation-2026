<?php

namespace App\Livewire\MasterData;

use App\Enums\RiskLevel;
use App\Livewire\Traits\HasNotification;
use App\Models\Module;
use App\Models\Competency;
use App\Models\Peralatan;
use App\Services\ModuleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class ModuleForm extends Component
{
    use WithFileUploads, AuthorizesRequests, HasNotification;

    public $moduleId;
    public $editMode = false;

    // Delete modals
    public $showDeleteWorkOrderItemModal = false;
    public $showDeleteWorkOrderSubitemModal = false;
    public $showDeleteWorkOrderReferenceModal = false;
    public $showDeletePersonelModal = false;
    public $showDeleteToolModal = false;
    public $showDeleteDeliverableModal = false;
    public $deletingWorkOrderItemIndex = null;
    public $deletingWorkOrderSubitemIndices = [];
    public $deletingWorkOrderReferenceIndex = null;
    public $deletingPersonelIndex = null;
    public $deletingToolIndex = null;
    public $deletingDeliverableIndex = null;

    // Track deleted IDs to exclude from wire:poll reload
    public $deletedWorkOrderReferenceIds = [];

    public $code;
    public $name;
    public $duration;
    public $risk_level = 'low';
    public $pricing_baseline;
    public $is_active = 1;
    public $notes;

    // Nested data structures
    public $workOrderItems = [];
    public $workOrderReferences = [];
    public $personels = [];
    public $tools = [];
    public $deliverables = [];

    public function mount($module = null)
    {
        if ($module) {
            $this->editMode = true;
            $this->moduleId = $module->id;
            $this->code = $module->code;
            $this->name = $module->name;
            $this->duration = $module->duration;
            $this->risk_level = $module->risk_level->value;
            $this->pricing_baseline = $module->pricing_baseline;
            $this->is_active = $module->is_active ? 1 : 0;
            $this->notes = $module->notes;

            $this->loadNestedData($module);
        } else {
            $this->authorize('create', Module::class);
        }
    }

    private function loadNestedData(Module $module): void
    {
        // Load work order items with subitems
        $this->workOrderItems = $module->workOrderItems->map(function ($item) {
            return [
                'id' => $item->id,
                'order' => $item->order,
                'name' => $item->name,
                'description' => $item->description,
                'nature' => $item->nature,
                'is_active' => $item->is_active,
                'subitems' => $item->subitems->map(function ($subitem) {
                    return [
                        'id' => $subitem->id,
                        'order' => $subitem->order,
                        'name' => $subitem->name,
                        'description' => $subitem->description,
                        'nature' => $subitem->nature,
                        'is_active' => $subitem->is_active,
                    ];
                })->toArray(),
            ];
        })->toArray();

        // Load work order references
        $this->workOrderReferences = $module->workOrderReferences->map(function ($ref) {
            return [
                'id' => $ref->id,
                'document_name' => $ref->document_name,
                'document_id' => $ref->document_id,
                'file_path' => $ref->file_path,
                'file_name' => $ref->file_name,
                'file_size' => $ref->file_size,
                'file_status' => $ref->file_status,
                'file_error' => $ref->file_error,
                'file' => null,
            ];
        })->toArray();

        // Load personels
        $this->personels = $module->personels->map(function ($personel) {
            return [
                'id' => $personel->id,
                'position_name' => $personel->position_name,
                'quantity' => $personel->quantity,
                'nature' => $personel->nature,
                'competencies' => $personel->competencies->pluck('id')->toArray(),
            ];
        })->toArray();

        // Load tools
        $this->tools = $module->tools->map(function ($tool) {
            return [
                'id' => $tool->id,
                'peralatan_id' => $tool->peralatan_id,
                'requires_calibration' => $tool->requires_calibration,
                'quantity' => $tool->quantity,
            ];
        })->toArray();

        // Load deliverables
        $this->deliverables = $module->deliverables->map(function ($del) {
            return [
                'id' => $del->id,
                'order' => $del->order,
                'name' => $del->name,
                'description' => $del->description,
                'nature' => $del->nature,
                'is_active' => $del->is_active,
            ];
        })->toArray();
    }

    public function rules()
    {
        $rules = [
            'code' => ['required', 'string', 'max:50', $this->editMode ? 'unique:modules,code,' . $this->moduleId : 'unique:modules,code'],
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:0',
            'risk_level' => 'required|in:' . implode(',', collect(RiskLevel::cases())->pluck('value')->toArray()),
            'pricing_baseline' => 'required|numeric|min:0',
            'is_active' => 'required|in:0,1',
            'notes' => 'nullable|string',
            'workOrderItems' => 'array',
            'workOrderReferences' => 'array',
            'personels' => 'array',
            'tools' => 'array',
            'deliverables' => 'array',
        ];

        // Validate work order references
        foreach ($this->workOrderReferences as $index => $reference) {
            $rules["workOrderReferences.{$index}.document_name"] = 'required|string|max:255';
            
            // Check if this is existing data with file or new data
            $hasExistingFile = isset($reference['file_name']) && !empty($reference['file_name']);
            $isExistingRecord = isset($reference['id']) && !str_starts_with($reference['id'], 'temp_');
            
            // Only validate file if it's a new record without existing file
            if (!$isExistingRecord && !$hasExistingFile) {
                $rules["workOrderReferences.{$index}.file"] = file_upload_validation_rule('work_order_reference', true);
            } elseif ($hasExistingFile) {
                $rules["workOrderReferences.{$index}.file"] = file_upload_validation_rule('work_order_reference', false);
            }
        }

        // Validate work order items
        foreach ($this->workOrderItems as $itemIndex => $item) {
            $rules["workOrderItems.{$itemIndex}.name"] = 'required|string|max:255';
            $rules["workOrderItems.{$itemIndex}.nature"] = 'required|in:mandatory,optional';
            $rules["workOrderItems.{$itemIndex}.is_active"] = 'required|in:0,1';

            // Validate subitems
            if (isset($item['subitems']) && is_array($item['subitems'])) {
                foreach ($item['subitems'] as $subitemIndex => $subitem) {
                    $rules["workOrderItems.{$itemIndex}.subitems.{$subitemIndex}.name"] = 'required|string|max:255';
                    $rules["workOrderItems.{$itemIndex}.subitems.{$subitemIndex}.nature"] = 'required|in:mandatory,optional';
                    $rules["workOrderItems.{$itemIndex}.subitems.{$subitemIndex}.is_active"] = 'required|in:0,1';

                    // If parent is mandatory, subitem must be mandatory
                    if (isset($item['nature']) && $item['nature'] === 'mandatory') {
                        $rules["workOrderItems.{$itemIndex}.subitems.{$subitemIndex}.nature"] = 'required|in:mandatory';
                    }
                }
            }
        }

        // Validate personels
        foreach ($this->personels as $personelIndex => $personel) {
            $rules["personels.{$personelIndex}.position_name"] = 'required|string|max:255';
            $rules["personels.{$personelIndex}.quantity"] = 'required|integer|min:1';
            $rules["personels.{$personelIndex}.nature"] = 'required|in:mandatory,optional';
        }

        // Validate tools
        foreach ($this->tools as $toolIndex => $tool) {
            $rules["tools.{$toolIndex}.peralatan_id"] = 'required|exists:peralatans,id';
            $rules["tools.{$toolIndex}.quantity"] = 'required|integer|min:1';
        }

        // Validate deliverables
        foreach ($this->deliverables as $delIndex => $deliverable) {
            $rules["deliverables.{$delIndex}.name"] = 'required|string|max:255';
            $rules["deliverables.{$delIndex}.nature"] = 'required|in:mandatory,optional';
            $rules["deliverables.{$delIndex}.is_active"] = 'required|in:0,1';
        }

        return $rules;
    }

    public function validationAttributes()
    {
        $attributes = [
            'code' => 'kode modul',
            'name' => 'nama modul',
            'duration' => 'durasi',
            'deliverable' => 'deliverable',
            'risk_level' => 'tingkat risiko',
            'pricing_baseline' => 'harga dasar',
            'is_active' => 'status aktif',
            'notes' => 'catatan',
        ];

        // Add dynamic attributes for work order references
        foreach ($this->workOrderReferences as $index => $reference) {
            $attributes["workOrderReferences.{$index}.document_name"] = 'nama dokumen referensi #' . ($index + 1);
            $attributes["workOrderReferences.{$index}.file"] = 'file referensi #' . ($index + 1);
        }

        // Add dynamic attributes for work order items
        foreach ($this->workOrderItems as $itemIndex => $item) {
            $attributes["workOrderItems.{$itemIndex}.name"] = 'nama item #' . ($itemIndex + 1);
            $attributes["workOrderItems.{$itemIndex}.nature"] = 'sifat item #' . ($itemIndex + 1);
            $attributes["workOrderItems.{$itemIndex}.is_active"] = 'status item #' . ($itemIndex + 1);
            
            if (isset($item['subitems']) && is_array($item['subitems'])) {
                foreach ($item['subitems'] as $subitemIndex => $subitem) {
                    $attributes["workOrderItems.{$itemIndex}.subitems.{$subitemIndex}.name"] = 'nama work order subitem #' . ($subitemIndex + 1) . ' dari item #' . ($itemIndex + 1);
                    $attributes["workOrderItems.{$itemIndex}.subitems.{$subitemIndex}.nature"] = 'sifat subitem #' . ($subitemIndex + 1) . ' dari item #' . ($itemIndex + 1);
                    $attributes["workOrderItems.{$itemIndex}.subitems.{$subitemIndex}.is_active"] = 'status subitem #' . ($subitemIndex + 1) . ' dari item #' . ($itemIndex + 1);
                }
            }
        }

        // Add dynamic attributes for personels
        foreach ($this->personels as $personelIndex => $personel) {
            $attributes["personels.{$personelIndex}.position_name"] = 'nama jabatan personel #' . ($personelIndex + 1);
            $attributes["personels.{$personelIndex}.quantity"] = 'jumlah personel #' . ($personelIndex + 1);
            $attributes["personels.{$personelIndex}.nature"] = 'sifat personel #' . ($personelIndex + 1);
        }

        // Add dynamic attributes for tools
        foreach ($this->tools as $toolIndex => $tool) {
            $attributes["tools.{$toolIndex}.peralatan_id"] = 'nama alat #' . ($toolIndex + 1);
            $attributes["tools.{$toolIndex}.quantity"] = 'jumlah alat #' . ($toolIndex + 1);
        }

        // Add dynamic attributes for deliverables
        foreach ($this->deliverables as $delIndex => $deliverable) {
            $attributes["deliverables.{$delIndex}.name"] = 'nama deliverable #' . ($delIndex + 1);
            $attributes["deliverables.{$delIndex}.nature"] = 'sifat deliverable #' . ($delIndex + 1);
            $attributes["deliverables.{$delIndex}.is_active"] = 'status deliverable #' . ($delIndex + 1);
        }

        return $attributes;
    }

    public function messages()
    {
        return [
            'code.required' => 'Kode modul wajib diisi',
            'code.unique' => 'Kode modul sudah digunakan',
            'code.max' => 'Kode modul maksimal 50 karakter',
            'name.required' => 'Nama modul wajib diisi',
            'name.max' => 'Nama modul maksimal 255 karakter',
            'duration.required' => 'Durasi wajib diisi',
            'duration.integer' => 'Durasi harus berupa angka',
            'duration.min' => 'Durasi minimal 0',
            'risk_level.required' => 'Tingkat risiko wajib dipilih',
            'risk_level.in' => 'Tingkat risiko tidak valid',
            'pricing_baseline.required' => 'Harga dasar wajib diisi',
            'pricing_baseline.numeric' => 'Harga dasar harus berupa angka',
            'pricing_baseline.min' => 'Harga dasar minimal 0',
            'is_active.required' => 'Status wajib dipilih',
            'is_active.in' => 'Status tidak valid',
            
            // Work Order References
            'workOrderReferences.*.document_name.required' => 'Nama dokumen referensi wajib diisi',
            'workOrderReferences.*.document_name.max' => 'Nama dokumen referensi maksimal 255 karakter',
            'workOrderReferences.*.file.required' => 'File referensi wajib diupload',
            'workOrderReferences.*.file.mimes' => 'File referensi harus berupa PDF, DOC, DOCX, XLS, atau XLSX',
            'workOrderReferences.*.file.max' => 'Ukuran file referensi maksimal 10MB',
            
            // Work Order Items
            'workOrderItems.*.name.required' => 'Nama item wajib diisi',
            'workOrderItems.*.name.max' => 'Nama item maksimal 255 karakter',
            'workOrderItems.*.nature.required' => 'Sifat item wajib dipilih',
            'workOrderItems.*.nature.in' => 'Sifat item tidak valid',
            'workOrderItems.*.is_active.required' => 'Status item wajib dipilih',
            'workOrderItems.*.is_active.in' => 'Status item tidak valid',
            
            // Work Order Subitems
            'workOrderItems.*.subitems.*.name.required' => 'Nama work order subitem wajib diisi',
            'workOrderItems.*.subitems.*.name.max' => 'Nama work order subitem maksimal 255 karakter',
            'workOrderItems.*.subitems.*.nature.required' => 'Sifat subitem wajib dipilih',
            'workOrderItems.*.subitems.*.nature.in' => 'Sifat subitem tidak valid',
            'workOrderItems.*.subitems.*.is_active.required' => 'Status subitem wajib dipilih',
            'workOrderItems.*.subitems.*.is_active.in' => 'Status subitem tidak valid',
            
            // Personels
            'personels.*.position_name.required' => 'Nama jabatan wajib diisi',
            'personels.*.position_name.max' => 'Nama jabatan maksimal 255 karakter',
            'personels.*.quantity.required' => 'Jumlah wajib diisi',
            'personels.*.quantity.integer' => 'Jumlah harus berupa angka',
            'personels.*.quantity.min' => 'Jumlah minimal 1',
            'personels.*.nature.required' => 'Sifat wajib dipilih',
            'personels.*.nature.in' => 'Sifat tidak valid',
            
            // Tools
            'tools.*.peralatan_id.required' => 'Nama alat wajib dipilih',
            'tools.*.peralatan_id.exists' => 'Alat yang dipilih tidak valid',
            'tools.*.quantity.required' => 'Jumlah wajib diisi',
            'tools.*.quantity.integer' => 'Jumlah harus berupa angka',
            'tools.*.quantity.min' => 'Jumlah minimal 1',
            
            // Deliverables
            'deliverables.*.name.required' => 'Nama deliverable wajib diisi',
            'deliverables.*.name.max' => 'Nama deliverable maksimal 255 karakter',
            'deliverables.*.nature.required' => 'Sifat deliverable wajib dipilih',
            'deliverables.*.nature.in' => 'Sifat deliverable tidak valid',
            'deliverables.*.is_active.required' => 'Status deliverable wajib dipilih',
            'deliverables.*.is_active.in' => 'Status deliverable tidak valid',
        ];
    }

    // Auto-update subitem nature when parent item nature changes
    public function updated($propertyName)
    {
        // Check if a work order item nature was updated
        if (preg_match('/^workOrderItems\.(\d+)\.nature$/', $propertyName, $matches)) {
            $itemIndex = $matches[1];
            
            // If item is now mandatory, set all subitems to mandatory
            if (isset($this->workOrderItems[$itemIndex]['nature']) && 
                $this->workOrderItems[$itemIndex]['nature'] === 'mandatory') {
                
                if (isset($this->workOrderItems[$itemIndex]['subitems']) && 
                    is_array($this->workOrderItems[$itemIndex]['subitems'])) {
                    
                    foreach ($this->workOrderItems[$itemIndex]['subitems'] as $subitemIndex => $subitem) {
                        $this->workOrderItems[$itemIndex]['subitems'][$subitemIndex]['nature'] = 'mandatory';
                    }
                }
            }
        }
    }

    // Helper methods for nested arrays
    public function addWorkOrderItem()
    {
        $this->workOrderItems[] = [
            'id' => 'temp_' . uniqid(),
            'order' => count($this->workOrderItems) + 1,
            'name' => '',
            'description' => '',
            'nature' => 'mandatory',
            'is_active' => true,
            'subitems' => [],
        ];
    }

    public function removeWorkOrderItem($index)
    {
        $this->deletingWorkOrderItemIndex = $index;
        $this->showDeleteWorkOrderItemModal = true;
    }

    public function confirmDeleteWorkOrderItem()
    {
        if ($this->deletingWorkOrderItemIndex !== null) {
            unset($this->workOrderItems[$this->deletingWorkOrderItemIndex]);
            $this->workOrderItems = array_values($this->workOrderItems);
            $this->reorderWorkOrderItems();
            $this->deletingWorkOrderItemIndex = null;
            $this->showDeleteWorkOrderItemModal = false;
        }
    }

    public function addWorkOrderSubitem($itemIndex)
    {
        $this->workOrderItems[$itemIndex]['subitems'][] = [
            'id' => 'temp_' . uniqid(),
            'order' => count($this->workOrderItems[$itemIndex]['subitems']) + 1,
            'name' => '',
            'description' => '',
            'nature' => $this->workOrderItems[$itemIndex]['nature'],
            'is_active' => true,
        ];
    }

    public function removeWorkOrderSubitem($itemIndex, $subitemIndex)
    {
        $this->deletingWorkOrderSubitemIndices = ['itemIndex' => $itemIndex, 'subitemIndex' => $subitemIndex];
        $this->showDeleteWorkOrderSubitemModal = true;
    }

    public function confirmDeleteWorkOrderSubitem()
    {
        if (!empty($this->deletingWorkOrderSubitemIndices)) {
            $itemIndex = $this->deletingWorkOrderSubitemIndices['itemIndex'];
            $subitemIndex = $this->deletingWorkOrderSubitemIndices['subitemIndex'];
            unset($this->workOrderItems[$itemIndex]['subitems'][$subitemIndex]);
            $this->workOrderItems[$itemIndex]['subitems'] = array_values($this->workOrderItems[$itemIndex]['subitems']);
            $this->reorderWorkOrderSubitems($itemIndex);
            $this->deletingWorkOrderSubitemIndices = [];
            $this->showDeleteWorkOrderSubitemModal = false;
        }
    }

    public function reorderWorkOrderItemsFromDrag($fromIndex, $toIndex)
    {
        if ($fromIndex === $toIndex) return;

        $item = $this->workOrderItems[$fromIndex];
        unset($this->workOrderItems[$fromIndex]);
        $this->workOrderItems = array_values($this->workOrderItems);

        // Insert at new position
        array_splice($this->workOrderItems, $toIndex, 0, [$item]);
        $this->reorderWorkOrderItems();
    }

    public function reorderWorkOrderSubitemsFromDrag($itemIndex, $fromIndex, $toIndex)
    {
        if ($fromIndex === $toIndex) return;

        $subitem = $this->workOrderItems[$itemIndex]['subitems'][$fromIndex];
        unset($this->workOrderItems[$itemIndex]['subitems'][$fromIndex]);
        $this->workOrderItems[$itemIndex]['subitems'] = array_values($this->workOrderItems[$itemIndex]['subitems']);

        // Insert at new position
        array_splice($this->workOrderItems[$itemIndex]['subitems'], $toIndex, 0, [$subitem]);
        $this->reorderWorkOrderSubitems($itemIndex);
    }

    public function reorderDeliverablesFromDrag($fromIndex, $toIndex)
    {
        if ($fromIndex === $toIndex) return;

        $deliverable = $this->deliverables[$fromIndex];
        unset($this->deliverables[$fromIndex]);
        $this->deliverables = array_values($this->deliverables);

        // Insert at new position
        array_splice($this->deliverables, $toIndex, 0, [$deliverable]);
        $this->reorderDeliverables();
    }

    public function addWorkOrderReference()
    {
        $this->workOrderReferences[] = [
            'id' => 'temp_' . uniqid(),
            'document_name' => '',
            'document_id' => '',
            'file_path' => '',
            'file_name' => null,
            'file_size' => null,
            'file_status' => null,
            'file_error' => null,
            'file' => null,
        ];
    }

    public function removeWorkOrderReference($index)
    {
        $this->deletingWorkOrderReferenceIndex = $index;
        $this->showDeleteWorkOrderReferenceModal = true;
    }

    public function confirmDeleteWorkOrderReference()
    {
        if ($this->deletingWorkOrderReferenceIndex !== null) {
            $item = $this->workOrderReferences[$this->deletingWorkOrderReferenceIndex] ?? null;
            
            // If it's an existing record (numeric ID), track it for deletion on save
            if ($item && isset($item['id']) && is_numeric($item['id'])) {
                $this->deletedWorkOrderReferenceIds[] = $item['id'];
            }
            
            unset($this->workOrderReferences[$this->deletingWorkOrderReferenceIndex]);
            $this->workOrderReferences = array_values($this->workOrderReferences);
            $this->deletingWorkOrderReferenceIndex = null;
            $this->showDeleteWorkOrderReferenceModal = false;
        }
    }

    public function removeReferenceFile($index)
    {
        if (isset($this->workOrderReferences[$index])) {
            $this->workOrderReferences[$index]['file'] = null;
        }
    }

    public function downloadWorkOrderReferenceFile($index)
    {
        $reference = $this->workOrderReferences[$index] ?? null;

        if (!$reference) {
            $this->notifyError('File tidak ditemukan.');
            return;
        }

        if (!isset($reference['file_path']) || !$reference['file_path']) {
            $this->notifyError('File tidak ditemukan.');
            return;
        }

        if (!\Storage::disk('local')->exists($reference['file_path'])) {
            $this->notifyError('File tidak ditemukan.');
            return;
        }

        // Generate new filename: modul_namareferensi
        $moduleName = $this->name ?? 'modul';
        $referenceName = $reference['document_name'] ?? 'referensi';

        // Get original file extension from file_path
        $originalExtension = pathinfo($reference['file_path'], PATHINFO_EXTENSION);
        $newFileName = "{$moduleName}_{$referenceName}.{$originalExtension}";

        return \Storage::disk('local')->download($reference['file_path'], $newFileName);
    }

    public function addPersonel()
    {
        $this->personels[] = [
            'id' => 'temp_' . uniqid(),
            'position_name' => '',
            'quantity' => 1,
            'nature' => 'mandatory',
            'competencies' => [],
        ];
    }

    public function removePersonel($index)
    {
        $this->deletingPersonelIndex = $index;
        $this->showDeletePersonelModal = true;
    }

    public function confirmDeletePersonel()
    {
        if ($this->deletingPersonelIndex !== null) {
            unset($this->personels[$this->deletingPersonelIndex]);
            $this->personels = array_values($this->personels);
            $this->deletingPersonelIndex = null;
            $this->showDeletePersonelModal = false;
        }
    }

    public function addTool()
    {
        $this->tools[] = [
            'id' => 'temp_' . uniqid(),
            'peralatan_id' => null,
            'requires_calibration' => false,
            'quantity' => 1,
        ];
    }

    public function removeTool($index)
    {
        $this->deletingToolIndex = $index;
        $this->showDeleteToolModal = true;
    }

    public function confirmDeleteTool()
    {
        if ($this->deletingToolIndex !== null) {
            unset($this->tools[$this->deletingToolIndex]);
            $this->tools = array_values($this->tools);
            $this->deletingToolIndex = null;
            $this->showDeleteToolModal = false;
        }
    }

    public function addDeliverable()
    {
        $this->deliverables[] = [
            'id' => 'temp_' . uniqid(),
            'order' => count($this->deliverables) + 1,
            'name' => '',
            'description' => '',
            'nature' => 'mandatory',
            'is_active' => true,
        ];
    }

    public function removeDeliverable($index)
    {
        $this->deletingDeliverableIndex = $index;
        $this->showDeleteDeliverableModal = true;
    }

    public function confirmDeleteDeliverable()
    {
        if ($this->deletingDeliverableIndex !== null) {
            unset($this->deliverables[$this->deletingDeliverableIndex]);
            $this->deliverables = array_values($this->deliverables);
            $this->reorderDeliverables();
            $this->deletingDeliverableIndex = null;
            $this->showDeleteDeliverableModal = false;
        }
    }

    private function reorderWorkOrderItems()
    {
        foreach ($this->workOrderItems as $index => $item) {
            $this->workOrderItems[$index]['order'] = $index + 1;
        }
    }

    private function reorderWorkOrderSubitems($itemIndex)
    {
        foreach ($this->workOrderItems[$itemIndex]['subitems'] as $index => $subitem) {
            $this->workOrderItems[$itemIndex]['subitems'][$index]['order'] = $index + 1;
        }
    }

    private function reorderDeliverables()
    {
        foreach ($this->deliverables as $index => $deliverable) {
            $this->deliverables[$index]['order'] = $index + 1;
        }
    }

    public function save(ModuleService $service)
    {
        $this->withValidator(function ($validator) {
            if ($validator->fails()) {
                $errors = $validator->errors();
                $allErrors = $errors->all();
                $message = count($allErrors) > 1 
                    ? 'Terdapat ' . count($allErrors) . ' kesalahan validasi' 
                    : $allErrors[0];
                $this->dispatch('notify', type: 'error', message: $message);

                // Notify collapsible sections/items so the ones containing
                // validation errors auto-expand for the user.
                $this->dispatch('module-validation-failed', errorKeys: array_keys($errors->messages()));
            }
        })->validate();

        try {
            $data = [
                'code' => $this->code,
                'name' => $this->name,
                'duration' => $this->duration,
                'risk_level' => $this->risk_level,
                'pricing_baseline' => $this->pricing_baseline,
                'is_active' => $this->is_active,
                'notes' => $this->notes,
                'work_order_items' => $this->workOrderItems,
                'work_order_references' => [],
                'personels' => $this->personels,
                'tools' => $this->tools,
                'deliverables' => $this->deliverables,
            ];

            foreach ($this->workOrderReferences as $reference) {
                if (empty($reference['document_name'])) {
                    continue;
                }

                $refData = [
                    'document_name' => $reference['document_name'],
                    'document_id' => $reference['document_id'] ?? null,
                ];

                if (isset($reference['file']) && $reference['file'] instanceof \Illuminate\Http\UploadedFile) {
                    $tempPath = $reference['file']->store('temp/work-order-references', 'local');
                    $refData['temp_file_path'] = $tempPath;
                    $refData['file_name'] = $reference['file']->getClientOriginalName();
                    $refData['id'] = $reference['id'] ?? null;
                } elseif (!empty($reference['file_path'])) {
                    $refData['file_path'] = $reference['file_path'];
                    $refData['file_name'] = $reference['file_name'];
                    $refData['file_size'] = $reference['file_size'];
                    $refData['id'] = $reference['id'] ?? null;
                } else {
                    $refData['id'] = $reference['id'] ?? null;
                }

                $data['work_order_references'][] = $refData;
            }

            if ($this->editMode) {
                $module = Module::findOrFail($this->moduleId);
                $this->authorize('update', $module);
                $service->update($module, $data);
                $message = 'Modul berhasil diupdate!';
            } else {
                $this->authorize('create', Module::class);
                $service->create($data);
                $message = 'Modul berhasil ditambahkan!';
            }

            $this->notifySuccess($message);
            return $this->redirect(route('master-data.modules.index'), navigate: true);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            \Log::error('Authorization error in ModuleForm: ' . $e->getMessage());
            $this->notifyError('Anda tidak memiliki izin untuk melakukan aksi ini.');
        } catch (\Exception $e) {
            \Log::error('Error in ModuleForm save: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'module_id' => $this->moduleId ?? null,
                'edit_mode' => $this->editMode,
            ]);
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function cancel()
    {
        return $this->redirect(route('master-data.modules.index'), navigate: true);
    }

    public function refreshWorkOrderReferenceFileStatus(): void
    {
        if ($this->moduleId) {
            $module = Module::find($this->moduleId);
            if ($module) {
                $this->loadWorkOrderReferencesFromDatabase($module);
            }
        }
    }

    protected function loadWorkOrderReferencesFromDatabase(Module $module): void
    {
        // Preserve new items (temp IDs) that haven't been saved yet
        $newItems = collect($this->workOrderReferences)->filter(function ($item) {
            return isset($item['id']) && str_starts_with($item['id'], 'temp_');
        })->toArray();

        // Load existing items from database, excluding deleted ones
        $existingItems = $module->workOrderReferences
            ->reject(function ($ref) {
                return in_array($ref->id, $this->deletedWorkOrderReferenceIds);
            })
            ->map(function ($ref) {
                return [
                    'id' => $ref->id,
                    'document_name' => $ref->document_name,
                    'document_id' => $ref->document_id,
                    'file_path' => $ref->file_path,
                    'file_name' => $ref->file_name,
                    'file_size' => $ref->file_size,
                    'file_status' => $ref->file_status,
                    'file_error' => $ref->file_error,
                    'file' => null,
                ];
            })->toArray();

        // Merge existing items with new items
        $this->workOrderReferences = array_merge($existingItems, $newItems);
    }

    public function render()
    {
        return view('livewire.master-data.module-form', [
            'riskLevels' => RiskLevel::cases(),
            'competencies' => Competency::active()->get(),
            'peralatans' => Peralatan::active()->orderBy('name')->get(),
            'module' => $this->editMode ? Module::find($this->moduleId) : null,
        ]);
    }
}
