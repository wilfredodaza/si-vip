<?php


namespace App\Traits;


trait ApiTrait
{

    public $paginate = [];

    public function pagination($model, $table)
    {

        $countTotals = $this->sortBy($model, $table)->countAllResults();
        $max = $this->request->getGet('per_page') ? $this->request->getGet('per_page') : 5;
        $pager = $this->request->getGet('pager') ? $this->request->getGet('pager') : 1 ;

        if ($pager == 1) {
            $link = [
                'next' => base_url('/api/products/list?pager=' . ($pager + 1))
            ];
        } else if ($pager == ceil($countTotals / $max)) {
            $link = [
                'previus' => base_url('/api/products/list?pager=' . ($pager - 1)),
            ];
        } else if ($pager >= 2) {
            $link = [
                'previus' => base_url('/api/products/list?pager=' . ($pager - 1)),
                'next' => base_url('/api/products/list?pager=' . ($pager + 1)),
            ];
        }

        $end = 0;
        if ($pager == 1) {
            $end = 0;
        } else if ($pager == 2) {
            $end = $max;
        } else if ($pager >= 3) {
            $end = $max * ($pager - 1);
        }

        if (($countTotals - $end) <= 0) {
            $real = $max - 1;
        } else {
            $real = abs(($countTotals - $end));
        }
        $this->paginate = [
            'total'         => $countTotals,
            'count'         => ($real > $max) ? $max : $real,
            'per_page'      => $max,
            'current_page'  => (int)$pager,
            'total_pages'   => ceil($countTotals / $max),
            'link'          => $link
        ];
        if($this->request->getGet('pager')){
            return $this->sortBy($model, $table)->limit(5, $end);
        }else {
            return $this->sortBy($model, $table);
        }
    }

    public function sortBy($model, $table)
    {

        $sortBy = $this->request->getGet('sort_by');
        $orderBy = $this->request->getGet('order_by');
        if ($sortBy && empty($orderBy)) {
            return $this->filter($model, $table)->orderBy($sortBy, 'ASC');
        } else if ($sortBy && $orderBy) {
            return $this->filter($model, $table)->orderBy($sortBy, $orderBy);
        }
        return $this->filter($model, $table);
    }

    public function filter($model, $table)
    {
        $data = $this->request->getGet();

        if ($data) {
            $params = [];
            foreach ($data as $item => $key) {
                if ($item != 'order_by' && $item != 'sort_by' && $item != 'pager' && $item != 'per_page') {
                    $params = array_merge([$table.'.'.$item => $key], $params);
                }

            }


            return $model->like($params);

        }
        return $model;
    }
}