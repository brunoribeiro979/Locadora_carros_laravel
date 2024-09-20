<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'imagem'];

    public function rules()
    {
        return [
            'nome' => 'required|unique:marcas,nome,' . $this->id . '|min:3',
            'imagem' => 'required|file|mimes:png,jpg'
        ];

        /*
        UNIQUE TEM 3 PARAMETROS
        1- tabela onde sera feita a pesquisa da existencia unica
        2- nome da coluna que será pesquisada na tabela
        3- id do registro que será desconsiderado na pesquisa (para casos de update no banco)
        */
    }

    public function feedback()
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'imagem.file' => 'A imagem deve ser um arquivo',
            'imagem.mimes' => 'O arquivo deve ser uma imagem do tipo PNG',
            'nome.unique' => 'O nome da marca já existe',
            'nome.min' => 'O nome deve ter no mínimo 3 caracteres'
        ];
    }

    public function modelos()
    {
        // uma marca possui muitos modelos
        return $this->hasMany('App\Models\Modelo');
    }
}
