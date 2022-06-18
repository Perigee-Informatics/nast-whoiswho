<div class="card">
    <div class="heading-report">
        <div class="row mt-2">
            <div class="col">
                <center>
                    <h5 class="font-weight-bold" style="text-decoration: underline">Members Data</h5>
            </div>
        </div>
        {{-- <div class="row">
            <div class="col" style="margin-top:-40px;">
                <a href="javascript:;" class="btn btn-sm btn-success la la-file-pdf float-right mr-3" onclick="printReport('PDF')"> Export to PDF</a>
            </div>
        </div> --}}
    </div>
    <div class="row mt-0">
        <div class="col-md-12">
            <div class="col" style="overflow:auto;">
                <table id="members_data_table" class="table table-bordered table-responsive table-striped mr-2 pr-2" style="background-color:#f8f9fa; overflow-x:scroll; display:inline-table !important;">
                    <thead>
                        <tr>
                            <th class="report-heading">Action</th>
                            <th class="report-heading">S.N.</th>
                            <th class="report-heading th_large">Client</th>
                            <th class="report-heading th_large">Product</th>
                            <th class="report-heading th_large">Province</th>
                            <th class="report-heading th_large">Contract F.Y.</th>
                            <th class="report-heading th_large">Contract Date</th>
                            <th class="report-heading th_large">AMC Start Date</th>
                            <th class="report-heading th_large">Contract Amount</th>
                            <th class="report-heading th_large">Taxable Amount</th>
                            <th class="report-heading th_large">VAT (Taxable amount * 13%)</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $total_contract_amount = 0;
                            $total_taxable_amount = 0;
                            $total_vat_amount = 0;
                        @endphp
                        @foreach($data as $row)
                            @php
                                $rowId = str_replace(' ','_',$row->product_name).'-'.$row->dt_client_id;
                                $total_contract_amount += $row->contract_amount;
                                $total_taxable_amount += $row->amc_excluding_vat;
                                $total_vat_amount += ($row->contract_amount-$row->amc_excluding_vat);
                                $color="#17a608";
                                $text_color="yellow";
                                if(isset($row->pending_fy)){
                                    if(count($row->pending_fy) == 1){
                                        $color="#b8f4fc";
                                        $text_color="blue";
                                    }
                                    if(count($row->pending_fy) > 1){
                                        $color="#fabebe";
                                        $text_color="darkred";
                                    }
                                }
                            @endphp
                            <tr data-toggle="collapse" data-target="{{ '#'.$rowId}}" class="accordion-toggle" style="{{'background-color:'.$color}}">
                                <td class="text-center"><button class="btn btn-light btn-sm px-2 mb-1"><i class="fa fa-eye p-0"></i></button></td>
                                <td class="report-data">{{$loop->iteration}}</td>

                                <td class="report-data">
                                    @if(isset($row->dt_client_product_id))
                                    <a style="{{'color:'.$text_color}}" target="_blank" href="{{backpack_url('sales/dtclient/'.$row->dt_client_id.'/dtclientproduct/'.$row->dt_client_product_id.'/edit')}}">
                                        {{$row->client_name}}
                                    </a>
                                    @else
                                    {{$row->client_name}}
                                    @endif
                                </td>
                                <td class="report-data">{{$row->product_name}}</td>
                                <td class="report-data">{{$row->province_name}}</td>
                                <td class="report-data">
                                    @if(isset($row->dt_client_product_id))
                                    <a style="{{'color:'.$text_color}}" target="_blank" href="{{backpack_url('sales/dtclient/'.$row->dt_client_id.'/dtclientproduct/'.$row->dt_client_product_id.'/product-contract/'.$row->contract_id.'/edit')}}">
                                        {{$row->contract_fiscal_year}}
                                    </a>
                                    @else
                                    {{$row->contract_fiscal_year}}
                                    @endif
                                </td>
                                <td class="report-data">{{convert_bs_from_ad($row->contract_date)}}</td>
                                <td class="report-data text-danger">
                                    @if(isset($row->dt_client_product_id))
                                    <a style="{{'color:'.$text_color}}" target="_blank" href="{{backpack_url('sales/dtclient/'.$row->dt_client_id.'/dtclientproduct/'.$row->dt_client_product_id.'/product-payment-collection')}}">
                                        {{convert_bs_from_ad($row->amc_start_date)}}
                                    </a>
                                    @else
                                    {{convert_bs_from_ad($row->amc_start_date)}}
                                    @endif
                                </td>
                                <td class="report-data num-data">{{$row->contract_amount}}</td>
                                <td class="report-data num-data">{{$row->amc_excluding_vat}}</td>
                                <td class="report-data num-data">{{$row->contract_amount-$row->amc_excluding_vat}}</td>
                            </tr>
                            <tr>
                                <td colspan="13" class="hiddenRow">
                                    <div class="accordian-body collapse ml-5 pr-2" id="{{$rowId}}"> 
                                    <table class="table table-striped table-bordered table-hover table-responsive my-4 mr-2" style="background-color:#eefdf9; display:inline-table !important;">
                                            <thead>
                                                <tr><th colspan="9" class="text-center bg-primary">{{ $row->client_name}}  ( AMC Details )</th></tr>
                                                <tr>
                                                    <th class="report-heading-second">S.N.</th>
                                                    <th class="report-heading-second th_large">Fiscal Year</th>
                                                    <th class="report-heading-second th_large">AMC %</th>
                                                    <th class="report-heading-second th_large">Cloud Cost</th>
                                                    <th class="report-heading-second th_large">AMC amount</th>
                                                    <th class="report-heading-second th_large">AMC Total (Cloud Cost + AMC Amount)</th>
                                                    <th class="report-heading-second th_large">AMC VAT (AMC Total * 13%)</th>
                                                    <th class="report-heading-second th_large">Total AMC with VAT</th>
                                                    <th class="report-heading-second th_large">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $j = 1;
                                                @endphp
                                                @foreach($row->amc_details as $amc)
                                                <tr>
                                                    <td class="report-data">{{  $j++ }}</td>
                                                    <td class="report-data">
                                                        @if(isset($row->dt_client_product_id))
                                                        <a target="_blank" href="{{backpack_url('sales/dtclient/'.$row->dt_client_id.'/dtclientproduct/'.$row->dt_client_product_id.'/product-payment-collection/'.$amc->id.'/edit')}}">
                                                            {{isset($amc->fiscalyearEntity) ? $amc->fiscalyearEntity->code : '-'}}
                                                        </a>
                                                        @else    
                                                            {{isset($amc->fiscalyearEntity) ? $amc->fiscalyearEntity->code : '-'}}
                                                        @endif
                                                    </td>
                                                    <td class="report-data-second">{{$row->amc_percentage}}</td>
                                                    <td class="report-data-second">{{$row->cloud_cost}}</td>
                                                    <td class="report-data-second">{{$row->amc_excluding_vat*($row->amc_percentage/100)}}</td>
                                                    <td class="report-data-second">{{round($row->amc_total,2)}}</td>
                                                    <td class="report-data-second">{{round($row->amc_total*0.13,2)}}</td>
                                                    <td class="report-data-second">{{round(($row->amc_total)+(($row->amc_total)*0.13),2)}}</td>
                                                    <td class="report-data-second bg-primary text-white">PAID</td>

                                                    
                                                </tr>
                                                @endforeach

                                                @if(isset($row->pending_fy))
                                                    @foreach($row->pending_fy as $fy)
                                                        <tr>
                                                            <td class="report-data">{{  $j++ }}</td>
                                                            <td class="report-data">
                                                                @if(isset($row->dt_client_product_id))
                                                                <a target="_blank" href="{{backpack_url('sales/dtclient/'.$row->dt_client_id.'/dtclientproduct/'.$row->dt_client_product_id.'/product-payment-collection')}}">
                                                                    {{$fy}}
                                                                </a>
                                                                @else    
                                                                    {{$fy}}
                                                                @endif
                                                            </td>
                                                            <td class="report-data-second">{{$row->amc_percentage}}</td>
                                                            <td class="report-data-second">{{$row->cloud_cost}}</td>
                                                            <td class="report-data-second">{{$row->amc_excluding_vat*($row->amc_percentage/100)}}</td>
                                                            <td class="report-data-second">{{round($row->amc_total,2)}}</td>
                                                            <td class="report-data-second">{{round($row->amc_total*0.13,2)}}</td>
                                                            <td class="report-data-second">{{round(($row->amc_total)+(($row->amc_total)*0.13),2)}}</td>
                                                            <td class="report-data-second bg-warning text-dark">PENDING</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div> 
                                </td>
                            </tr>
                        @endforeach    
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="report-heading text-center" colspan="8">TOTAL</th>
                            <th class="report-heading th_footer">{{ $total_contract_amount}}</th>
                            <th class="report-heading th_footer">{{ $total_taxable_amount}}</th>
                            <th class="report-heading th_footer">{{ $total_vat_amount}}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

<style>
    .report-heading {
        /* text-align: center; */
        font-size: 14px;
        /* font-family: 'Kalimati'; */
    }
    .report-heading-second{
        font-size:13px;
        padding:0px;
        background-color: rgb(9, 155, 9) !important;
    }

    .report-data {
        font-size: 14px;
        font-weight: 600;
        color: black;
        /* font-family: 'Kalimati'; */
    }
    .report-data-second{
        text-align: right;
        padding-right:20px !important;
    }

    tr>th {
        border-bottom: 1px solid white !important;
        border-right: 1px solid white !important;
        background-color: #3B72A0 !important;
        color: white;
    }
    tr>td {
        border-bottom: 1px solid grey !important;
        border-right: 1px solid grey !important;
    }
    tr>td:hover{
        cursor: pointer;
    }

    .th_large {
        min-width: 100px !important;
    }
    .num-data{
        text-align: right;
    }
    .th_footer{
        text-align: right;
        font-size:16px;
    }
    .table th{
        padding:.5rem .75rem !important;
    }
</style>