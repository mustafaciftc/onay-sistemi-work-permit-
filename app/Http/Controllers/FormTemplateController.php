<?php

namespace App\Http\Controllers;

use App\Models\FormTemplate;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormTemplateController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $templates = FormTemplate::where('company_id', $user->companies->first()->id)
            ->latest()
            ->get();

        return view('form-templates.index', compact('templates'));
    }

    public function create()
    {
        $fieldTypes = (new FormTemplate())->field_types;
        $workflowSteps = (new FormTemplate())->workflow_steps;

        return view('form-templates.create', compact('fieldTypes', 'workflowSteps'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array',
            'fields.*.label' => 'required|string',
            'fields.*.type' => 'required|string',
            'fields.*.required' => 'boolean',
            'workflow' => 'required|array',
            'workflow.*' => 'string',
            'is_default' => 'boolean',
        ]);

        $company = Auth::user()->companies->first();

        // Eğer bu template default yapılıyorsa, diğerlerini default'tan çıkar
        if ($request->boolean('is_default')) {
            FormTemplate::where('company_id', $company->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $template = FormTemplate::create([
            'company_id' => $company->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'fields' => $validated['fields'],
            'workflow' => $validated['workflow'],
            'is_default' => $request->boolean('is_default'),
        ]);

        return redirect()->route('form-templates.show', $template)
            ->with('success', 'Form şablonu başarıyla oluşturuldu.');
    }

    public function show(FormTemplate $template)
    {
        $this->authorize('view', $template);

        return view('form-templates.show', compact('template'));
    }

    public function edit(FormTemplate $template)
    {
        $this->authorize('update', $template);

        $fieldTypes = (new FormTemplate())->field_types;
        $workflowSteps = (new FormTemplate())->workflow_steps;

        return view('form-templates.edit', compact('template', 'fieldTypes', 'workflowSteps'));
    }

    public function update(Request $request, FormTemplate $template)
    {
        $this->authorize('update', $template);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array',
            'fields.*.label' => 'required|string',
            'fields.*.type' => 'required|string',
            'fields.*.required' => 'boolean',
            'workflow' => 'required|array',
            'workflow.*' => 'string',
            'is_default' => 'boolean',
        ]);

        // Eğer bu template default yapılıyorsa, diğerlerini default'tan çıkar
        if ($request->boolean('is_default')) {
            FormTemplate::where('company_id', $template->company_id)
                ->where('id', '!=', $template->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $template->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'fields' => $validated['fields'],
            'workflow' => $validated['workflow'],
            'is_default' => $request->boolean('is_default'),
        ]);

        return redirect()->route('form-templates.show', $template)
            ->with('success', 'Form şablonu başarıyla güncellendi.');
    }

    public function destroy(FormTemplate $template)
    {
        $this->authorize('delete', $template);

        // Default template silinemez
        if ($template->is_default) {
            return redirect()->back()->with('error', 'Varsayılan şablon silinemez.');
        }

        $template->delete();

        return redirect()->route('form-templates.index')
            ->with('success', 'Form şablonu başarıyla silindi.');
    }

    public function setDefault(FormTemplate $template)
    {
        $this->authorize('update', $template);

        FormTemplate::where('company_id', $template->company_id)
            ->where('is_default', true)
            ->update(['is_default' => false]);

        $template->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Varsayılan şablon olarak ayarlandı.');
    }

    public function togglePublish(FormTemplate $template)
    {
        $this->authorize('update', $template);

        $template->update([
            'is_published' => !$template->is_published
        ]);

        $status = $template->is_published ? 'yayınlandı' : 'yayından kaldırıldı';

        return redirect()->back()->with('success', "Form şablonu {$status}.");
    }
}
