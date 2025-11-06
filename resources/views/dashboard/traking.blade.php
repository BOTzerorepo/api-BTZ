<style>
  .layout {
      display: flex;
      gap: 20px;
  }
  
  #map {
      height: 500px;
      width: 60%;
      border-radius: 8px;
  }
  
  .timeline-box {
      width: 40%;
      max-height: 500px;
      overflow-y: auto;
      border-left: 2px solid #eee;
      padding-left: 10px;
      font-family: Arial, sans-serif;
  }
  
  .timeline li {
      margin-bottom: 6px;
      font-size: 14px;
  }
  
  .coord { color: #0077cc; }
  .event { color: #cc7700; }
  </style>
  
<h2>Detalle del contenedor {{ $equipmentReference }}</h2>

<div class="layout">
    <div class="timeline-box">
        <ul class="timeline">
        @foreach($data as $row)
            <li>
                <strong>{{ $row->ts }}</strong> ·
                @if($row->type === 'coord')
                    🛰️ <span class="coord">({{ $row->lat }}, {{ $row->longitude }})</span>
                @else
                    📦 <span class="event">{{ $row->event_type }} / {{ $row->transport_event_type_code }} / {{ $row->equipment_event_type_code }}</span>
                @endif
            </li>
        @endforeach
        </ul>
    </div>
</div>

