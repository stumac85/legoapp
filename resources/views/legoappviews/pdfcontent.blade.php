@extends('layout.pdfgen')

@section('content')
<div class="row">
    <div class="col">
        <div class="media mb-5">
        <a href="{{ $oSet->results->set_img_url }}" data-toggle="lightbox" class="row-img-link"><img src="{{ $oSet->results->set_img_url }}" class="mr-3" alt="{{ $oSet->results->set_num }}" style="width:200px;"></a>
            <div class="media-body">
                <h1 class="mt-0">{{ $oSet->results->name }} ({{ $oSet->results->set_num }})</h1>
                <strong>Set contains {{ $oSet->results->num_parts }} parts.</strong> Released in {{ $oSet->results->year }}. Includes the following spare parts: <br/>
                @foreach ($aSpares as $sparedata)
                    @if ($sparedata['image'])
                        <figure class="figure">
                            <a href="{{ $sparedata['image'] }}" data-toggle="lightbox" class="row-img-link" data-footer="{{ $sparedata['name'] }} {{ $sparedata['partnum'] }}"><img src="{{ $sparedata['image'] }}" alt="{{ $sparedata['partnum'] }}" style="width:50px;"/></a>
                            <figcaption class="figure-caption text-center">x {{ $sparedata['qty'] }}</figcaption>
                        </figure>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <table class="table table-bordered" style="font-size:1em; text-align:center;">
                <thead class="thead-light">
                    <tr>
                    <th style="width:40%; text-align:left;" class="p-0">Part name &amp; number</th>
                    <th style="width:5%" class="p-0">Image</th>
                    <th style="width:5%" class="p-0">Part Code</th>
                    <th style="width:20%" class="p-0">Colour</th>
                    <th style="width:15%" class="p-0">Number I need</th>
                    <th style="width:15%" class="p-0">Number I have</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($aParts as $partdata)
                <tr>
                    @if (strlen($partdata['name'])>50)
                        <td style="text-align:left;" class="p-0">{{ substr($partdata['name'],0,50) }}...</td>
                    @else
                        <td style="text-align:left;" class="p-0">{{ $partdata['name'] }}</td>
                    @endif
                    <td class="p-0">
                        @if ($partdata['image'])
                        <a href="{{ $partdata['image'] }}" data-toggle="lightbox" class="row-img-link" data-footer="{{ $partdata['name'] }} {{ $partdata['partnum'] }}"><img src="{{ $partdata['image'] }}" alt="{{ $partdata['partnum'] }}" style="width:30px;"/></a>
                        @endif
                    </td>
                    <td class="p-0">{{ $partdata['partnum'] }}</td>
                    <td class="p-0">{{ $partdata['color'] }}</td>
                    <th scope="row" class="p-0">{{ $partdata['qty'] }}</td>
                    <td class="p-0" id="{{ $partdata['partnum'] }}" interaction="true"><input type="number" name="quantity" id="qty_{{ $partdata['partnum'] }}" min="0" max="{{ $partdata['qty'] }}" style="width:50px;"></td>
                </tr>
                @endforeach
                </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
  window.onload = function () {
    $("td[interaction='true']").on("click",function(){
        var theInput = 'qty_' + $(this).attr('id');
        $('#'+theInput).focus();
    });
  }
</script>
@endsection

