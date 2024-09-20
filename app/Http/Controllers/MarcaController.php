<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{

    protected $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $marcaRepository = new MarcaRepository($this->marca);

        if ($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:id,' . $request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else {
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }


        if ($request->has('filtro')) {
            $marcaRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $marcaRepository->selectAtributos($request->atributos);
        }


        return response()->json($marcaRepository->getResultadoPaginado(3), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate($this->marca->rules(), $this->marca->feedback());
        $imagem = $request->file('imagem');
        // o metodo abaixo faz armazenar imagens dentro de storage/app/public (cria uma pasta escrita imagens e dentro dela coloca a imagem com nome unico, é gerado meio que uma hash), o armazenamento é local e pode ser feito tambem pela amazon na nuvem S3 da aws (para configurar é na pasta config/filesystems.php)
        $imagem_urn = $imagem->store('imagens', 'public');


        // É IMPORTANTE LA NO VALIDATE QUE ESTA NO MODEL, COLOCAR O TIPO DE ARQUIVO QUE PODE SER ACEITO PRA SALVAR, QUE NO CASO DE IMAGENS É PNG,JPG...EXEMPLO ABAIXO:
        // 'imagem' => 'required|file|mimes:png,jpg'


        // OUTRA COISA IMPORTANTE É APONTAR NO DIRETORIO PUBLIC DA RAIZ O DIRETORIO ONDE ESTAO AS IMAGENS PARA QUE POSSAM SER VISUALIZADAS, PARA ISSO É SÓ RODAR O COMANDO NO TERMINAL: php artisan storage:link

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = $this->marca->with('modelos')->find($id);
        if ($marca === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe!'], 404);
        }
        return response()->json($marca, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // print_r($request->all());
        // echo '<hr>';
        // print_r($marca->getAttributes());

        $marca = $this->marca->find($id);


        if ($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe!'], 404);
        }

        // PATCH SERVE PARA UPDATE PARCIAL E PUT TOTAL
        if ($request->method() === 'PATCH') {

            $regrasDinamicas = array();


            // percorrendo todas as regras definidas no model
            foreach ($marca->rules() as $input => $regra) {

                // coletar apenas as regras aplicáveis aos paramêtros parciais da requisição PATCH
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }


            $request->validate($regrasDinamicas, $marca->feedback());
        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }


        // preenchendo o objeto marca com todos os dados do request
        $marca->fill($request->all());

        // caso a imagem tenha sido enviada na requisicao
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);  // remove o arquivo antigo, caso um novo arquivo tenha sido enviado no request

            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens', 'public');
            $marca->imagem = $imagem_urn;
        }
        $marca->save();

        return response()->json($marca, 200);

        /*
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        // preencher o objeto marca com os dados do request
        $marca->fill($request->all());
        $marca->imagem = $imagem_urn;
        $marca->save();

        return response()->json($marca, 200);
     */
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // print_r($marca->getAttributes());

        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe!'], 404);
        }

        // remove o arquivo antigo, caso um novo arquivo tenha sido enviado no request
        Storage::disk('storage')->delete($marca->imagem);


        $marca->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
    }
}
