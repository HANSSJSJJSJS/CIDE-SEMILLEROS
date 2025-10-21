<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Campos comunes
            'nombre'   => ['required','string','max:255'],
            'apellido' => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'role' => ['required','in:ADMIN,LIDER GENERAL,LIDER SEMILLERO,APRENDIZ'],
            'password' => ['required','confirmed','min:8'],

            // -------------------------------
            // LÍDER SEMILLERO
            // -------------------------------
            'tipo_documento_lider'    => ['required_if:role,LIDER SEMILLERO','in:CC,CE'],
            'numero_documento_lider'  => ['required_if:role,LIDER SEMILLERO','string','max:50'],

            // -------------------------------
            // APRENDIZ
            // -------------------------------
            'tipo_documento_aprendiz'    => ['required_if:role,APRENDIZ','in:CC,TI,CE'],    
            'numero_documento_aprendiz'  => ['required_if:role,APRENDIZ','string','max:50'],
            'ficha'                    => ['required_if:role,aprendiz','string','max:50'],
            'programa_formacion'       => ['required_if:role,aprendiz','string','max:255'],
            'celular'                  => ['required_if:role,aprendiz','string','max:20'],
            'correo_institucional'     => ['required_if:role,aprendiz','email','max:255'],
            'contacto_emergencia'      => ['required_if:role,aprendiz','string','max:255'],
            'numero_contrato'          => ['required_if:role,aprendiz','string','max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            // Campos comunes
            'nombre.required'   => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'El correo no tiene un formato válido.',
            'email.unique'      => 'Este correo ya está registrado.',
            'role.required'     => 'Debes seleccionar un rol.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed'=> 'La confirmación de contraseña no coincide.',
            'password.min'      => 'La contraseña debe tener al menos :min caracteres.',

            // Líder Semillero
            'tipo_documento_lider.required_if'   => 'El tipo de documento es obligatorio para Líder Semillero.',
            'numero_documento_lider.required_if' => 'El número de documento es obligatorio para Líder Semillero.',

            // Aprendiz
            'tipo_documento_aprendiz.required_if'   => 'El tipo de documento es obligatorio para Aprendiz.',
            'numero_documento_aprendiz.required_if' => 'El documento es obligatorio para Aprendiz.',
            'ficha.required_if'                    => 'La ficha es obligatoria para Aprendiz.',
            'programa_formacion.required_if'       => 'El programa de formación es obligatorio para Aprendiz.',
            'celular.required_if'                  => 'El celular es obligatorio para Aprendiz.',
            'correo_institucional.required_if'     => 'El correo institucional es obligatorio para Aprendiz.',
            'correo_institucional.email'           => 'El correo institucional no tiene un formato válido.',
            'contacto_emergencia.required_if'      => 'El contacto de emergencia es obligatorio para Aprendiz.',
            'numero_contrato.required_if'          => 'El número de contrato es obligatorio para Aprendiz.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre'                 => 'nombre',
            'apellido'               => 'apellido',
            'email'                  => 'correo',
            'role'                   => 'rol',
            'password'               => 'contraseña',
            'password_confirmation'  => 'confirmación de contraseña',
            'tipo_documento_lider'   => 'tipo de documento (líder)',
            'numero_documento_lider' => 'número de documento (líder)',
            'tipo_documento_aprendiz'=> 'tipo de documento (aprendiz)',
            'numero_documento_aprendiz'=> 'número de documento (aprendiz)',
            'ficha'                  => 'ficha',
            'programa_formacion'     => 'programa de formación',
            'celular'                => 'celular',
            'correo_institucional'   => 'correo institucional',
            'contacto_emergencia'    => 'contacto de emergencia',
            'numero_contrato'        => 'número de contrato',
        ];
    }
}
