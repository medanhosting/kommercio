@include('backend.master.form.fields.text', [
    'name' => 'name',
    'label' => 'Name',
    'key' => 'name',
    'attr' => [
        'class' => 'form-control',
        'id' => 'name'
    ],
    'required' => TRUE
])

@include('backend.master.form.fields.text', [
    'name' => 'slug',
    'label' => 'Machine Name',
    'key' => 'slug',
    'attr' => [
        'class' => 'form-control',
        'id' => 'slug',
        'data-slug_source' => '#name'
    ],
    'required' => TRUE
])