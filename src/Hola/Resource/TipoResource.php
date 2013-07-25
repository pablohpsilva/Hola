<?php
namespace Hola\Resource;

use Hola\Service\TipoService,
    Tonic\Resource,
    Tonic\Response;
/**
 * @uri /tipo
 * @uri /tipo/:id
 */
class TipoResource extends Resource {

    private $tipoService = null;

    /**
     * @method GET
     * @provides application/json
     * @json
     * @param int $id
     * @return Tonic\Response
     */
    public function buscar($id = null) {
        try {
            $this->tipoService = new TipoService();
            if(is_null($id))
                return new Response(Response::OK, $this->tipoService->search());
            else
                return new Response(Response::OK, $this->tipoService->search($id));

        } catch (RADUFU\DAO\NotFoundException $e) {
            throw new Tonic\NotFoundException();
        }
    }


    /**
     * @method POST
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function criar($id = null) {
        if(!(isset($this->request->data->nome)))
            throw new Tonic\MethodNotAllowedException();

        try {
            $this->tipoService = new TipoService();
            $this->tipoService->post( $this->request->data->nome );
            
            $criada = $this->tipoService->search($this->request->data->nome)->getNome();

            unset($this->tipoService);
            return new Response(Response::CREATED, array('nome' => $criada));

        } catch (RADUFU\DAO\Exception $e) {
            //throw new Tonic\Exception($e->getMessage());
            throw new Tonic\Exception("Este tipo ja existe. Tente um outro nome.");
        }
    }

    /**
     * @method PUT
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function atualizar($id = null) {
        if(is_null($this->request->data->nome))
            throw new Tonic\MethodNotAllowedException();
        
        try {
            $this->tipoService = new TipoService();
            $this->tipoService->update(
                    $this->request->data->nome
                    );

            unset($this->tipoService);
            return new Response(Response::OK);

        } catch (RADUFU\DAO\NotFoundException $e) {
            throw new Tonic\NotFoundException();
        } catch (RADUFU\DAO\DAO\Exception $e) {
            throw new Tonic\Exception($e->getMessage());
        }

    }

    /**
     * @method DELETE
     * @provides application/json
     * @json
     * @return Tonic\Response
     */
    public function remover($id = null) {
        if(is_null($this->request->data->nome))
            throw new Tonic\MethodNotAllowedException();

        try {
            $this->tipoService = new TipoService();
            $this->tipoService->delete($this->request->data->nome);

            unset($this->tipoService);
            return new Response(Response::OK);

        } catch (RADUFU\DAO\NotFoundException $e) {
            //throw new Tonic\Exception($e->getMessage());
            throw new Tonic\Exception("Este tipo nao foi encontrado. Ele ja nao foi deletado?");
        }
    }

    /**
     * Transforma as requisições json para array e as repostas array para json
     */

    protected function json() {

        $this->before(function ($request) {
            if ($request->contentType == 'application/json') {
                $request->data = json_decode($request->data);
            }
        });

        $this->after(function ($response) {
         $response->contentType = 'application/json';
         $response->body = json_encode($response->body);
     });
    }
}

?>
