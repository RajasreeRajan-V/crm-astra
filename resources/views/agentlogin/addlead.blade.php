@extends('agentlogin.layout')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Card with Soft Shadow and Rounded Borders -->
            <div class="card shadow-lg border-0 rounded-4">
                <!-- Card Header with Gradient Background -->
                <div class="card-header text-white rounded-top-4" style="background: linear-gradient(135deg, #6c63ff, #a098ff);">
                    <h5 class="mb-0 text-center">Add New Lead</h5>
                </div>
                <div class="card-body">
                    {{-- Display Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form with Styled Inputs and Buttons -->
                    <form action="{{ route('agent.store.lead') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">Name</label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg shadow-sm @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                required 
                                placeholder="Enter lead name" 
                                value="{{ old('name') }}"
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="contact" class="form-label fw-bold">Contact</label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg shadow-sm @error('contact') is-invalid @enderror" 
                                id="contact" 
                                name="contact" 
                                required 
                                placeholder="Enter contact details" 
                                value="{{ old('contact') }}"
                            >
                            @error('contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input 
                                type="email" 
                                class="form-control form-control-lg shadow-sm @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                placeholder="Enter email" 
                                value="{{ old('email') }}"
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
    <label for="source" class="form-label fw-bold">Source</label>
    <select 
        class="form-select form-select-lg shadow-sm @error('source') is-invalid @enderror" 
        id="source" 
        name="source"
        onchange="toggleReferralField()"
    >
        <option value="" disabled selected>Select source</option>
        <option value="Website" {{ old('source') == 'Website' ? 'selected' : '' }}>Website</option>
        <option value="Social Media" {{ old('source') == 'Social Media' ? 'selected' : '' }}>Social Media</option>
        <option value="Email" {{ old('source') == 'Email' ? 'selected' : '' }}>Email</option>
        <option value="Referral" {{ old('source') == 'Referral' ? 'selected' : '' }}>Referral</option>
        <option value="Advertisement" {{ old('source') == 'Advertisement' ? 'selected' : '' }}>Advertisement</option>
    </select>
    @error('source')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Referral Person Dropdown (Initially Hidden) -->
<div class="mb-4" id="referralContainer" style="display: none;">
    <label for="referral_id" class="form-label fw-bold">Referral Person</label>
    <select 
        class="form-select form-select-lg shadow-sm @error('referral_id') is-invalid @enderror" 
        id="referral_id" 
        name="referral_id"
    >
        <option value="">Select referring lead</option>
        @foreach ($leads as $lead)
            <option value="{{ $lead->id }}" {{ old('referral_id') == $lead->id ? 'selected' : '' }}>
                {{ $lead->name }}
            </option>
        @endforeach
    </select>
    @error('referral_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
                        <div class="mb-4">
    <label for="deal_item" class="form-label fw-bold">Deal Item</label>
    <input 
        type="text" 
        class="form-control form-control-lg shadow-sm @error('deal_item') is-invalid @enderror" 
        id="deal_item" 
        name="deal_item" 
        placeholder="Enter deal item (optional)" 
        value="{{ old('deal_item') }}"
    >
    @error('deal_item')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label for="rate" class="form-label fw-bold">Estimated Rate</label>
    <input 
        type="number" 
        step="0.01" 
        class="form-control form-control-lg shadow-sm @error('rate') is-invalid @enderror" 
        id="rate" 
        name="rate" 
        placeholder="Enter estimated rate (optional)" 
        value="{{ old('rate') }}"
    >
    @error('rate')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label class="form-label fw-bold">Assigned Agent</label>
    <input 
        type="text" 
        class="form-control form-control-lg shadow-sm" 
        value="{{ auth()->guard('agent')->user()->name }}" 
        readonly
    >
    <input type="hidden" name="assigned_agent_id" value="{{ auth()->guard('agent')->user()->id }}">
</div>

                        <div class="d-grid">
                            <!-- Styled Button -->
                            <button type="submit" class="btn btn-lg btn-primary rounded-pill shadow-lg" style="background: linear-gradient(135deg, #6c63ff, #a098ff); border: none;">
                                Add Lead
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleReferralField() {
        var source = document.getElementById("source").value;
        var referralContainer = document.getElementById("referralContainer");
        
        if (source === "Referral") {
            referralContainer.style.display = "block";
        } else {
            referralContainer.style.display = "none";
            document.getElementById("referral_id").value = ""; // Reset referral ID when hidden
        }
    }
</script>
@endsection
