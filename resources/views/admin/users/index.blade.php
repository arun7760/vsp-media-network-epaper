@extends('layouts.admin')

@section('title', 'వినియోగదారులు & పాత్రలు - VSP TIMES E-Paper')
@section('header_title', 'న్యూస్‌రూమ్ సిబ్బంది & అనుమతులు (Users & Roles)')

@section('content')
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h2 style="font-family: 'Ramabhadra', sans-serif; font-size: 22px; color: var(--admin-navy);">
                <i class="fa-solid fa-users-gear" style="color: var(--admin-gold); margin-right: 8px;"></i>
                వినియోగదారుల నిర్వహణ
            </h2>
            <p style="color: var(--admin-muted); font-size: 13px; margin-top: 4px;">
                ఎడిటోరియల్ డెస్క్ సిబ్బంది, రివ్యూవర్లు మరియు అడ్మినిస్ట్రేటర్ల ఖాతాలు మరియు పాత్రలు (RBAC).
            </p>
        </div>
        <button type="button" class="btn-admin btn-gold" onclick="document.getElementById('createUserModal').style.display='flex';">
            <i class="fa-solid fa-user-plus"></i> నూతన వినియోగదారుని చేర్చండి
        </button>
    </div>

    <!-- Users Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: #F8FAFC; border-bottom: 2px solid var(--admin-border); color: var(--admin-navy);">
                    <th style="padding: 14px 20px; font-weight: 700;">పేరు (Name)</th>
                    <th style="padding: 14px 20px; font-weight: 700;">ఈమెయిల్ (Email)</th>
                    <th style="padding: 14px 20px; font-weight: 700;">పాత్ర (Role)</th>
                    <th style="padding: 14px 20px; font-weight: 700;">స్థితి (Status)</th>
                    <th style="padding: 14px 20px; font-weight: 700;">చివరి లాగిన్</th>
                    <th style="padding: 14px 20px; font-weight: 700; text-align: right;">చర్యలు</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr style="border-bottom: 1px solid var(--admin-border);">
                        <td style="padding: 14px 20px; font-weight: 700; color: var(--admin-navy);">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #E2E8F0; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; color: var(--admin-navy);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                {{ $user->name }}
                            </div>
                        </td>
                        <td style="padding: 14px 20px; color: #475569;">
                            {{ $user->email }}
                        </td>
                        <td style="padding: 14px 20px;">
                            @php
                                $roleColors = [
                                    'super_admin' => ['bg' => '#FEF3C7', 'color' => '#92400E', 'label' => 'Super Admin'],
                                    'admin' => ['bg' => '#EFF6FF', 'color' => '#1E40AF', 'label' => 'Administrator'],
                                    'editor' => ['bg' => '#F3E8FF', 'color' => '#6B21A8', 'label' => 'Editor'],
                                    'approver' => ['bg' => '#ECFDF5', 'color' => '#065F46', 'label' => 'Approver'],
                                    'reviewer' => ['bg' => '#ECFDF5', 'color' => '#065F46', 'label' => 'Reviewer'],
                                    'publisher' => ['bg' => '#FCE7F3', 'color' => '#9D174D', 'label' => 'Publisher'],
                                    'viewer' => ['bg' => '#F1F5F9', 'color' => '#475569', 'label' => 'Viewer'],
                                ];
                                $c = $roleColors[$user->role] ?? ['bg' => '#F1F5F9', 'color' => '#475569', 'label' => $user->role];
                            @endphp
                            <span style="background: {{ $c['bg'] }}; color: {{ $c['color'] }}; padding: 4px 10px; border-radius: 999px; font-weight: 700; font-size: 11px;">
                                {{ $c['label'] }}
                            </span>
                        </td>
                        <td style="padding: 14px 20px;">
                            @if($user->is_active)
                                <span style="color: #059669; font-weight: 700; font-size: 12px;">
                                    <i class="fa-solid fa-circle" style="font-size: 8px;"></i> క్రియాశీలం
                                </span>
                            @else
                                <span style="color: #DC2626; font-weight: 700; font-size: 12px;">
                                    <i class="fa-solid fa-circle" style="font-size: 8px;"></i> నిలిపివేయబడింది
                                </span>
                            @endif
                        </td>
                        <td style="padding: 14px 20px; color: #94A3B8; font-size: 12px;">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'ఇంకా లేదు' }}
                        </td>
                        <td style="padding: 14px 20px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <button type="button" class="btn-admin btn-outline" style="padding: 5px 10px; font-size: 11px;"
                                        onclick="openEditUserModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->role }}', {{ $user->is_active ? '1' : '0' }});">
                                    <i class="fa-solid fa-pen-to-square"></i> సవరించండి
                                </button>

                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-admin btn-danger" style="padding: 5px 10px; font-size: 11px;"
                                                onclick="return confirm('ఈ యూజర్‌ను ఖచ్చితంగా తొలగించాలనుకుంటున్నారా?');">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="padding: 16px 20px; border-top: 1px solid var(--admin-border);">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div id="createUserModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 10px; width: 100%; max-width: 520px; padding: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <h3 style="font-family: 'Ramabhadra', sans-serif; font-size: 18px; color: var(--admin-navy); margin-bottom: 16px;">
            <i class="fa-solid fa-user-plus" style="color: var(--admin-gold); margin-right: 6px;"></i>
            నూతన వినియోగదారుని నమోదు చేయండి
        </h3>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">పూర్తి పేరు *</label>
                <input type="text" name="name" required placeholder="ఉదా: రాము శర్మ"
                       style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">ఈమెయిల్ చిరునామా *</label>
                <input type="email" name="email" required placeholder="name@vspmedianetwork.com"
                       style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">పాత్ర (Role) *</label>
                    <select name="role" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px; background: #FFFFFF;">
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">పాస్‌వర్డ్ *</label>
                    <input type="password" name="password" required minlength="8" placeholder="కనీసం 8 అక్షరాలు"
                           style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-admin btn-outline" onclick="document.getElementById('createUserModal').style.display='none';">రద్దు</button>
                <button type="submit" class="btn-admin btn-gold">ఖాతా సృష్టించండి</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 10px; width: 100%; max-width: 520px; padding: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <h3 style="font-family: 'Ramabhadra', sans-serif; font-size: 18px; color: var(--admin-navy); margin-bottom: 16px;">
            <i class="fa-solid fa-user-pen" style="color: var(--admin-gold); margin-right: 6px;"></i>
            యూజర్ వివరాలు సవరించండి
        </h3>

        <form id="editUserForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">పూర్తి పేరు *</label>
                <input type="text" name="name" id="edit_name" required
                       style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">ఈమెయిల్ చిరునామా *</label>
                <input type="email" name="email" id="edit_email" required
                       style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">పాత్ర (Role) *</label>
                    <select name="role" id="edit_role" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px; background: #FFFFFF;">
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">స్థితి (Status) *</label>
                    <select name="is_active" id="edit_is_active" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px; background: #FFFFFF;">
                        <option value="1">క్రియాశీలం (Active)</option>
                        <option value="0">నిలిపివేయబడింది (Inactive)</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">నూతన పాస్‌వర్డ్ (మార్చాలనుకుంటేనే)</label>
                <input type="password" name="password" minlength="8" placeholder="ఖాళీగా ఉంచితే పాతదే ఉంటుంది"
                       style="width: 100%; padding: 10px 12px; border: 1px solid var(--admin-border); border-radius: 6px; font-family: inherit; font-size: 13px;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-admin btn-outline" onclick="document.getElementById('editUserModal').style.display='none';">రద్దు</button>
                <button type="submit" class="btn-admin btn-primary">మార్పులను సేవ్ చేయండి</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditUserModal(id, name, email, role, isActive) {
        const form = document.getElementById('editUserForm');
        form.action = '/admin/users/' + id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_is_active').value = isActive;
        document.getElementById('editUserModal').style.display = 'flex';
    }
</script>
@endpush
