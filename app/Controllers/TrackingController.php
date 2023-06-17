<?php


namespace App\Controllers;


use App\Models\Invoice;
use App\Models\Notification;
use App\Models\TrackingCustomer;

class TrackingController extends BaseController
{
    public function quotation($id = null)
    {
        $trackingCustomer = new TrackingCustomer();
        $data = $trackingCustomer
            ->where(['companies_id' => session('user')->companies_id, 'table_id' => $id, '`type_tracking_id' => 1])
            ->orderBy('id', 'desc')
            ->get()
            ->getResult();
        $invoice = new Invoice();
        $invoicesTracking = $invoice->select('invoices.invoice_status_id')
            ->where('id', $id)
            ->get()
            ->getResult();
        return view('quotation/tracking', ['id' => $id, 'data' => $data, 'tracking' => $invoicesTracking]);
    }

    public function create($id = null, $typeTracking = null)
    {
        $typeTrackingData = $this->_typeTracking($typeTracking);
        try {

            $data = [
                'message' => $this->request->getPost('message'),
                'username' => session('user')->username,
                'created_at' => date('Y-m-d H:i:s'),
                'table_id' => $id,
                'companies_id' => session('user')->companies_id,
                'type_tracking_id' => $typeTrackingData['id'],
            ];

            $tracking = new TrackingCustomer();
            $tracking->save($data);
            if ($this->request->getPost('notification') == 'on') {
                $notificacion = new Notification();
                $data = [
                    'title' => 'Seguimiento ' . $typeTrackingData['title'] . ' ' . $id,
                    'body' => $this->request->getPost('message') . ' <br>' . $typeTrackingData['body'] . ' ' . $id,
                    'icon' => 'receipt',
                    'color' => 'cyan',
                    'companies_id' => session('user')->companies_id,
                    'status' => 'Active',
                    'created_at' => $this->request->getPost('created_at'),
                    'view' => 'false',
                    'type_document_id' => $typeTrackingData['idDocument'],
                    'url' => $typeTrackingData['url']
                ];

                $notificacion->save($data);
            }
            return redirect()->to(base_url() . '/' . $typeTrackingData['url'] . '/' . $id)->with('success', 'Los datos se guardaron correctamente.');
        } catch (\Exception $e) {
            return redirect()->to(base_url() . '/' . $typeTrackingData['url'] . '/' . $id)->with('error', $e->getMessage());
        }
    }

    public function edit($id = null)
    {
        $trackingCustomer = new TrackingCustomer();
        $data = $trackingCustomer->find(['id' => $id]);
        echo json_encode($data[0]);
        die();
    }


    public function update($id = null, $typeTracking = null, $idTracking = null)
    {
        $typeTrackingData = $this->_typeTracking($typeTracking);
        try {
            $data = [
                'message' => $this->request->getPost('message'),
                'username' => session('user')->username,
                'created_at' => date('Y-m-d H:i:s'),
                'table_id' => $id,
                'companies_id' => session('user')->companies_id,
                'type_tracking_id' => $typeTrackingData['id']
            ];

            $tracking = new TrackingCustomer();
            $tracking->update(['id' => $idTracking], $data);

            return redirect()->to(base_url() . '/' . $typeTrackingData['url'] . '/' . $id)->with('success', 'Los datos se guardaron correctamente.');
        } catch (\Exception $e) {
            return redirect()->to(base_url() . '/' . $typeTrackingData['url'] . '/' . $id)->with('error', $e->getMessage());
        }
    }

    public function _typeTracking($id, $customer = null)
    {

        switch ($id) {
            case 'purchaseOrder':
                $typeTracking = [
                    'id' => 4,
                    'title' => 'Orden de Compra N°',
                    'body' => 'Orden de compra Relacionada N°',
                    'url' => 'purchaseOrder/tracking',
                    'idDocument' => 114
                ];
                break;
            case 'customer':
                $typeTracking = [
                    'id' => 5,
                    'title' => 'Cambio de frecuencia',
                    'body' => "El cliente {$customer['name']} cambio su frecuencia de {$customer['fa']} a {$customer['fn']} ",
                    'url' => 'customers/profile',
                    'idDocument' => $customer['id']
                ];
                break;
            default:
                $typeTracking = [
                    'id' => 1,
                    'title' => 'Cotización N°',
                    'body' => 'Cotización Relacionada N°',
                    'url' => 'tracking/quotation',
                    'idDocument' => 100
                ];
                break;
        }
        return $typeTracking;
    }
}