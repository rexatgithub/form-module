<?php namespace Modules\Form\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Form\Entities\Form;
use Modules\Form\Repositories\FormRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;

class FormController extends AdminBaseController
{
    /**
     * @var FormRepository
     */
    private $form;

    public function __construct(FormRepository $form)
    {
        parent::__construct();

        $this->form = $form;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //$forms = $this->form->all();

        return view('form::admin.forms.index', compact(''));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('form::admin.forms.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->form->create($request->all());

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('form::forms.title.forms')]));

        return redirect()->route('admin.form.form.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Form $form
     * @return Response
     */
    public function edit(Form $form)
    {
        return view('form::admin.forms.edit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Form $form
     * @param  Request $request
     * @return Response
     */
    public function update(Form $form, Request $request)
    {
        $this->form->update($form, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('form::forms.title.forms')]));

        return redirect()->route('admin.form.form.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Form $form
     * @return Response
     */
    public function destroy(Form $form)
    {
        $this->form->destroy($form);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('form::forms.title.forms')]));

        return redirect()->route('admin.form.form.index');
    }
}
