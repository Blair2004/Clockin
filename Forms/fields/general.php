<?php

use App\Services\Helper;
use App\Models\Role;

return [
    'label'     =>      __( 'Genral' ),
    'fields'    =>      [
        [
            'type'  =>  'date',
            'label' =>  __( 'Start Date' ),
            'name'  =>  'from_moment',
            'description'   =>  __( 'Determine from which date the report start' ),
        ], [
            'type'  =>  'date',
            'label' =>  __( 'End Date' ),
            'name'  =>  'to_moment',
            'description'   =>  __( 'Determine from which date the report end' ),
        ], [
            'type'          =>  'select',
            'label'         =>  __( 'Worker' ),
            'name'          =>  'user_id',
            'options'       =>  Helper::toJsOptions( Role::namespace( 'employee' )->users, [ 'id', 'username' ]),
            'description'   =>  __( 'Determine from which date the report start' ),
        ], 
    ]
];