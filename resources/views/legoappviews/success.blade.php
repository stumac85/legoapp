<div class="modal-header">
    <h5 class="modal-title">Matching Set(s)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
</div>
<div class="modal-body">
    @foreach ($aSorted as $count => $sets)
    <h3>Sets containing {{ $count }} items from your selection</h3>
    <table class="table table-hover table-sm">
        <thead class="thead-light">
            <tr>
            <th style="width:10%">Set</th>
            <th style="width:40%">Name</th>
            <th style="width:20%">Parts included</th>
            <th style="width:30%">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($sets as $setnum => $setdata)
        @php
            $setcode = explode('-',$setnum);
        @endphp
        <tr>
            <th scope="row">{{ $setnum }}</th>
            <td>
                {{ $setdata['name'] }} ({{ $setdata['year'] }})<br/>Total of {{ number_format($setdata['parts']) }} parts
                <!--<a href="{{ $setdata['image'] }}" data-toggle="lightbox" class="row-img-link" data-footer="{{ $setdata['name'] }}">View image &raquo;</a>-->
            </td>
            <td>
                @foreach ($setdata['parts_included'] as $partdata)
                    {{ $partdata['part_num'] }} ({{ $partdata['color_name'] }})<br/>
                @endforeach
            </td>
            <td><a href="{{ $setdata['external'] }}" target="_blank" rel="noopener noreferrer">View on Rebrickable &raquo;</a><br/>
            <a href="/samples/legoapp/print/{{ $setnum }}" target="_blank" rel="noopener noreferrer">Printable parts list (detailed)</a><br/>
            <a href="/samples/legoapp/print/{{ $setnum }}?view=1" target="_blank" rel="noopener noreferrer">Printable parts list (basic)</a><br/>
            <a href="https://www.lego.com/en-us/service/buildinginstructions/search#?search&text={{ htmlentities($setcode[0]) }}" target="_blank" rel="noopener noreferrer">Search for instructions &raquo;</a><br/>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endforeach
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>