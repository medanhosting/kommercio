@extends('backend.master.form.fields.master')

@section('form_field')
    <?php
    $attr['class'] = (isset($attr['class'])?$attr['class']:'').' district-select';
    $attr['data-first_option'] = 'Select District';
    $defaultOptions = isset($defaultOptions)?$defaultOptions:null;
    $active_only = isset($active_only)?$active_only:false;

    $options = ['' => $attr['data-first_option']] + AddressHelper::getDistrictOptions($parent, $active_only);
    ?>

    {!! Form::select($name, $options, $defaultOptions, $attr) !!}
@overwrite