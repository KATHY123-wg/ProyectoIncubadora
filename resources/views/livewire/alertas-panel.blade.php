<div wire:poll.5s class="bg-white shadow rounded-lg p-4 border border-gray-200">

    <h3 class="text-lg font-bold text-gray-700 mb-4">
        Alertas Activas
    </h3>

    @if($alertas->isEmpty())
        <div class="p-3 text-gray-600 bg-gray-50 border rounded">
            No hay alertas abiertas en este momento.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-3 py-2 border">Fecha</th>
                        <th class="px-3 py-2 border">Incubadora</th>
                        <th class="px-3 py-2 border">Proceso</th>
                        <th class="px-3 py-2 border">Tipo</th>
                        <th class="px-3 py-2 border">Código</th>
                        <th class="px-3 py-2 border">Nivel</th>
                        <th class="px-3 py-2 border">Mensaje</th>
                        <th class="px-3 py-2 border">Valor / Umbral</th>
                        <th class="px-3 py-2 border text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($alertas as $a)
                        <tr class="@if($a->nivel==='critical') bg-red-50 
                                   @elseif($a->nivel==='warning') bg-yellow-50 
                                   @else bg-gray-50 @endif">
                            <td class="px-3 py-2 border">{{ $a->fecha_registro->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-2 border">{{ $a->incubadora->codigo ?? '—' }}</td>
                            <td class="px-3 py-2 border">{{ $a->proceso->nombre ?? '—' }}</td>
                            <td class="px-3 py-2 border">{{ $a->tipo }}</td>
                            <td class="px-3 py-2 border font-mono">{{ $a->codigo }}</td>
                            <td class="px-3 py-2 border font-semibold">
                                @if($a->nivel==='critical')
                                    <span class="text-red-700">CRÍTICO</span>
                                @elseif($a->nivel==='warning')
                                    <span class="text-yellow-700">ADVERTENCIA</span>
                                @else
                                    <span class="text-gray-600">INFO</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 border">{{ $a->mensaje }}</td>
                            <td class="px-3 py-2 border">
                                @if(!is_null($a->valor_actual))
                                    {{ $a->valor_actual }}
                                    @if(!is_null($a->umbral)) / {{ $a->umbral }} @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-3 py-2 border text-center">
                                <button wire:click="resolver({{ $a->id }})"
                                    class="px-2 py-1 text-xs border rounded bg-green-100 hover:bg-green-200">
                                    Resolver
                                </button>
                                <button wire:click="silenciar({{ $a->id }},60)"
                                    class="px-2 py-1 text-xs border rounded bg-gray-100 hover:bg-gray-200 ml-1">
                                    Silenciar 1h
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
