@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Müşteri Yönetimi</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Ad</th>
                            <th>Mail</th>
                            <th class="text-end pe-4">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="fw-semibold ps-4">{{ $user->id }}</td>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td><span class="badge bg-secondary">{{ $user->email }}</span></td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2 align-items-center">
                                    
                                    <form action="{{ route('admin.user.toggle', $user->id) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-success' : 'btn-warning' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Pasif' }}
                                        </button>
                                    </form>

                                    
                                    <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Henüz kayıtlı müşteri bulunmuyor.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection