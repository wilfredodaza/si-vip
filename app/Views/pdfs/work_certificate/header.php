<table width="100%">
    <tr>
        <td style="width: 25%;" class="text-center vertical-align-top">
        </td>
        <td style="width: 50%; padding: 0 1rem;" class="text-center vertical-align-top">
            <div>
                <h2><?= $company->company ?></h6>   
                <h4>NIT: <?= $company->identification_number ?></h6>   
            </div>
        </td>
        <td style="width: 25%; text-align: right;" class="vertical-align-top">
            <img  style="width: 136px; height: 100;" src="<?= base_url('assets/upload/imgs/'.$company->logo) ?>" alt="logo">
        </td>
    </tr>
</table>