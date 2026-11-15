@if(session('success') || session('error'))
    <div class="toast-stack">
        @foreach(['success' => 'success', 'error' => 'danger'] as $key => $tone)
            @if(session($key))
                <div class="toast align-items-center text-bg-{{ $tone }} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi {{ $tone === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle' }} me-1"></i>
                            {{ session($key) }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger d-flex gap-2 align-items-start" role="alert">
        <i class="bi bi-exclamation-octagon fs-5"></i>
        <div>
            <div class="fw-semibold mb-1">Revisa la información:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
