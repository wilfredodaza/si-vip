<?php
namespace App\Controllers\Transformer;


use League\Fractal;

class ProductTransformer extends Fractal\TransformerAbstract
{
    private $validParams = ['limit', 'order'];

    public function transform(Object $product)
    {

        return [
            '_id'           => (int) $product->id,
            'code'          => $product->code,
            'value'         => (double) $product->valor,
            'description'   => $product->description,
            'brandName'     => $product->brandname,
            'modelName'     => $product->modelname,
            'unitMeasure'   => [
                '_id'       => $product->unit_measures_id,
                'uri'       => base_url('/api/v1/product/edit/'.$product->unit_measures_id),
            ],
            'typeItemIdentification'   => [
                '_id'       => (int) $product->type_item_identifications_id,
                'uri'       => base_url('/api/v1/product/edit/'.$product->type_item_identifications_id),
            ],
            'entryCredit' => [
                '_id'       => (int) $product->entry_credit,
                'uri'       => base_url('/api/v1/product/edit/'.$product->entry_credit),
            ],
            'entryDebit' => [
                '_id'       => (int) $product->entry_debit,
                'uri'       => base_url('/api/v1/product/edit/'.$product->entry_debit),
            ],
            'IVA' => [
                '_id'       => (int) $product->iva,
                'uri'       => base_url('/api/v1/product/edit/'.$product->iva),
            ],
            'reteFuente' => [
                '_id'       => (int) $product->retefuente,
                'uri'       => base_url('/api/v1/product/edit/'.$product->retefuente),
            ],
            'reteICA' => [
                '_id'       => (int) $product->reteica,
                'uri'       => base_url('/api/v1/product/edit/'.$product->reteica),
            ],
            'reteIVA' => [
                '_id'       => (int) $product->reteiva,
                'uri'       => base_url('/api/v1/product/edit/'.$product->reteiva),
            ],
            'links' => [
                [
                    'rel' => 'self',
                    'uri' => base_url('/api/v1/product/edit/'.$product->id),
                ]
            ],
        ];
    }
}