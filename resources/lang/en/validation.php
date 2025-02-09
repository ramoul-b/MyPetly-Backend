<?php
//validation.php
return [
    'name_required' => 'The name field is required.',
    'email_required' => 'The email field is required.',
    'email_invalid' => 'The email format is invalid.',
    'email_unique' => 'The email is already taken.',
    'password_required' => 'The password field is required.',
    'password_min' => 'The password must be at least :min characters.',
    'password_confirmed' => 'The password confirmation does not match.',

    'photo_image' => 'The file must be an image.',
    'photo_mimes' => 'The file must be in jpeg, png, or jpg format.',
    'photo_max' => 'The image must not exceed :max kilobytes.',


    'required' => 'The :attribute field is required.',
    'image' => 'The :attribute must be an image.',
    'mimes' => 'The :attribute must be a file of type: :values.',
    'max' => 'The :attribute may not be greater than :max kilobytes.',
    'unique' => 'The :attribute has already been taken.',
    'date' => 'The :attribute is not a valid date.',

    'required' => 'The :attribute field is required.',
    'unique' => 'The :attribute must be unique.',
    'exists' => 'The selected :attribute is invalid.',
    'url' => 'The :attribute must be a valid URL.',

    'sex.required' => 'The sex field is required.',
    'sex.in' => 'The sex must be either male or female.',
    'color.required' => 'The color field is required.',
    'weight.numeric' => 'The weight must be a valid number.',
    'weight.min' => 'The weight must be at least :min kg.',
    'weight.max' => 'The weight must not exceed :max kg.',
    'height.numeric' => 'The height must be a valid number.',
    'height.min' => 'The height must be at least :min cm.',
    'height.max' => 'The height must not exceed :max cm.',
    'identification_number.required' => 'The identification number field is required.',
    'identification_number.unique' => 'The identification number has already been taken.',

];
