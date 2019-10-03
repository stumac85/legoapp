@extends('layout.pdfgen')

@section('content')
<div class="row">
    <div class="col">
        <h1>{{ $oSet->results->name }} ({{ $oSet->results->set_num }})</h1>
    </div>
</div>
<div class="row">
    <div class="col">
    @php
    $prev_color = '';
    @endphp
        <p>
        @foreach ($aParts as $partdata)
        @php
            $current_color = $partdata['color'];
            if($current_color!=$prev_color){
                echo '</p>'.'<h4 class="m-0 p-0">'.$current_color.'</h4><p class="m-0 p-0">';
                $prev_color = $current_color;
            }
        @endphp
        <figure class="figure border m-0 p-0">
            @if ($partdata['image'])
                <img src="{{ $partdata['image'] }}" alt="{{ $partdata['partnum'] }}" style="width:70px;"/>
            @else
                <span style="width:70px; height:70px; display:block; text-align:center; padding-top:40%; font-weight:bold; font-size:0.7em;">{{ $partdata['partnum'] }}</span>
            @endif
            <figcaption class="figure-caption text-center">x {{ $partdata['qty'] }}</figcaption>
        </figure>
        @endforeach
        </p>
    </div>
</div>
@endsection