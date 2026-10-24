@extends('layouts.admin')

@section('title', (app()->getLocale() === 'en' ? 'Upload New Edition' : 'కొత్త సంచిక అప్‌లోడ్') . ' - VSP TIMES E-Paper')
@section('header_title', app()->getLocale() === 'en' ? 'Upload New E-Paper Edition' : 'కొత్త ఈ-పేపర్ సంచిక అప్‌లోడ్')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 24px; font-weight: 800; color: var(--admin-navy);">
                <i class="fa-solid fa-cloud-arrow-up" style="color: var(--admin-gold); margin-right: 8px;"></i>
                {{ app()->getLocale() === 'en' ? 'PDF Upload & Processing Desk' : 'పీడీఎఫ్ అప్‌లోడ్ & ప్రాసెసింగ్ డెస్క్' }}
            </h2>
            <p style="color: var(--admin-muted); font-size: 14px; margin-top: 4px;">
                {{ app()->getLocale() === 'en' ? 'Upload daily broadsheet PDF to automatically decompose and prepare pages for readers.' : 'రోజువారీ ఈ-పేపర్ పీడీఎఫ్ ఫైల్‌ను అప్‌లోడ్ చేసి, పేజీలను ఆటోమేటిక్‌గా సంగ్రహించండి.' }}
            </p>
        </div>
        <a href="{{ route('admin.editions.index') }}" class="btn-admin btn-outline">
            <i class="fa-solid fa-arrow-left"></i> {{ app()->getLocale() === 'en' ? 'Back' : 'వెనుకకు' }}
        </a>
    </div>

    <div class="card">
        <form action="{{ route('admin.editions.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <!-- Region Selection -->
                <div>
                    <label style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 8px; color: var(--admin-navy);">
                        {{ app()->getLocale() === 'en' ? 'Regional Edition' : 'ప్రాంతీయ ఎడిషన్ (Region)' }} <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="region_id" id="region_id" required style="width: 100%; padding: 11px 14px; border: 1px solid {{ $errors->has('region_id') ? '#DC2626' : 'var(--admin-border)' }}; border-radius: 6px; font-size: 14px; font-family: inherit; background: #FFFFFF;">
                        <option value="">{{ app()->getLocale() === 'en' ? '-- Select Region --' : '-- ప్రాంతాన్ని ఎంచుకోండి --' }}</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'en' ? $region->display_name : $region->name_te . ' (' . $region->name_en . ')' }}
                            </option>
                        @endforeach
                    </select>
                    @error('region_id')
                        <div style="color: #DC2626; font-size: 13px; margin-top: 6px; font-weight: 600;">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Edition Date -->
                <div>
                    <label style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 8px; color: var(--admin-navy);">
                        {{ app()->getLocale() === 'en' ? 'Publication Date' : 'ప్రచురణ తేదీ (Publication Date)' }} <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="date" name="edition_date" id="edition_date" required 
                           value="{{ old('edition_date', now('Asia/Kolkata')->toDateString()) }}"
                           style="width: 100%; padding: 11px 14px; border: 1px solid {{ $errors->has('edition_date') ? '#DC2626' : 'var(--admin-border)' }}; border-radius: 6px; font-size: 14px; font-family: inherit;">
                    @error('edition_date')
                        <div style="color: #DC2626; font-size: 13px; margin-top: 6px; font-weight: 600; line-height: 1.4;">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Edition Title -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 8px; color: var(--admin-navy);">
                    {{ app()->getLocale() === 'en' ? 'Edition Title' : 'సంచిక శీర్షిక (Edition Title)' }} <span style="color: #DC2626;">*</span>
                </label>
                <input type="text" name="title" id="title" required 
                       value="{{ old('title', app()->getLocale() === 'en' ? 'VSP TIMES Main Edition' : 'విఎస్పీ టైమ్స్ ప్రధాన సంచిక') }}"
                       placeholder="{{ app()->getLocale() === 'en' ? 'e.g. Visakhapatnam Main Edition' : 'ఉదా: విశాఖపట్నం ప్రధాన సంచిక' }}"
                       style="width: 100%; padding: 11px 14px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 14px; font-family: inherit;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px;">
                <!-- Volume Number -->
                <div>
                    <label style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 8px; color: var(--admin-navy);">
                        {{ app()->getLocale() === 'en' ? 'Volume Number' : 'సంపుటి (Volume Number)' }}
                    </label>
                    <input type="text" name="volume_number" value="{{ old('volume_number', app()->getLocale() === 'en' ? 'Vol 1' : 'సంపుటి 1') }}"
                           style="width: 100%; padding: 11px 14px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 14px; font-family: inherit;">
                </div>

                <!-- Issue Number -->
                <div>
                    <label style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 8px; color: var(--admin-navy);">
                        {{ app()->getLocale() === 'en' ? 'Issue Number' : 'సంచిక సంఖ్య (Issue Number)' }}
                    </label>
                    <input type="text" name="issue_number" value="{{ old('issue_number', app()->getLocale() === 'en' ? 'Issue 1' : 'సంచిక 1') }}"
                           style="width: 100%; padding: 11px 14px; border: 1px solid var(--admin-border); border-radius: 6px; font-size: 14px; font-family: inherit;">
                </div>
            </div>

            <!-- PDF Drag & Drop Zone -->
            <div style="margin-bottom: 28px;">
                <label style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 8px; color: var(--admin-navy);">
                    {{ app()->getLocale() === 'en' ? 'Master PDF Document' : 'ఈ-పేపర్ పీడీఎఫ్ ఫైల్ (Master PDF Document)' }} <span style="color: #DC2626;">*</span>
                </label>
                
                <div id="dropZone" style="border: 2px dashed #CBD5E1; border-radius: 12px; padding: 40px 24px; text-align: center; background: #F8FAFC; cursor: pointer; transition: all 0.2s;">
                    <i class="fa-solid fa-file-pdf" style="font-size: 48px; color: #DC2626; margin-bottom: 14px;"></i>
                    <h3 style="font-size: 16px; font-weight: 700; color: var(--admin-navy); margin-bottom: 6px;">
                        {{ app()->getLocale() === 'en' ? 'Drag & Drop newspaper PDF here, or click to browse' : 'పీడీఎఫ్ ఫైల్‌ను ఇక్కడ లాగి వదలండి (Drag & Drop) లేదా క్లిక్ చేయండి' }}
                    </h3>
                    <p style="font-size: 13px; color: var(--admin-muted); margin-bottom: 14px;">
                        {{ app()->getLocale() === 'en' ? 'Maximum file size up to 100MB (%PDF- binary signature is verified).' : 'మరియు పరిమాణం గరిష్టంగా 100MB వరకు అనుమతించబడుతుంది. (%PDF- బైనరీ చెక్ చేయబడుతుంది).' }}
                    </p>
                    <input type="file" name="pdf_file" id="pdf_file" accept="application/pdf,.pdf" required style="display: none;">
                    
                    <button type="button" onclick="document.getElementById('pdf_file').click();" class="btn-admin btn-outline" style="background: #FFFFFF;">
                        <i class="fa-solid fa-folder-open"></i> {{ app()->getLocale() === 'en' ? 'Browse PDF from Computer' : 'ఫైల్‌ను కంప్యూటర్ నుండి ఎంచుకోండి' }}
                    </button>

                    <div id="fileSelectedInfo" style="display: none; margin-top: 18px; padding: 12px; background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 8px; color: #065F46; font-size: 14px; font-weight: 600;">
                        <i class="fa-solid fa-circle-check"></i> <span id="fileName"></span> (<span id="fileSize"></span>)
                    </div>
                </div>
                @error('pdf_file')
                    <div style="color: #DC2626; font-size: 13px; margin-top: 6px; font-weight: 600;">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Progress Bar (Hidden initially) -->
            <div id="uploadProgressContainer" style="display: none; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--admin-navy);">
                    <span><i class="fa-solid fa-spinner fa-spin"></i> {{ app()->getLocale() === 'en' ? 'Uploading PDF & decomposing pages...' : 'పీడీఎఫ్ అప్‌లోడ్ & పేజీలు విడదీయబడుతున్నాయి...' }}</span>
                    <span id="progressText">0%</span>
                </div>
                <div style="height: 10px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                    <div id="progressBar" style="height: 100%; width: 0%; background: linear-gradient(90deg, var(--admin-navy), var(--admin-gold)); transition: width 0.3s;"></div>
                </div>
                <p style="font-size: 12px; color: var(--admin-muted); margin-top: 6px;">
                    {{ app()->getLocale() === 'en' ? '* Decomposing multi-page documents may take a few seconds. Please keep this window open.' : '* పెద్ద ఫైళ్ల విషయంలో పేజీల డికంపోజిషన్ కోసం కొన్ని సెకన్లు పట్టవచ్చు. దయచేసి విండోను మూసివేయవద్దు.' }}
                </p>
            </div>

            <!-- Publishing Status Option -->
            <div style="background: #F8FAFC; border: 1px solid var(--admin-border); border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; font-size: 14px; margin-bottom: 10px; color: var(--admin-navy);">
                    <i class="fa-solid fa-satellite-dish" style="color: var(--admin-red); margin-right: 6px;"></i>
                    {{ app()->getLocale() === 'en' ? 'Publication Status after Upload:' : 'అప్‌లోడ్ చేసిన తర్వాత ప్రచురణ స్థితి:' }}
                </label>
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; cursor: pointer; color: #065F46;">
                        <input type="radio" name="publish_mode" value="publish_now" checked style="accent-color: #10B981; width: 16px; height: 16px;">
                        <span><i class="fa-solid fa-bolt" style="color: #10B981;"></i> {{ app()->getLocale() === 'en' ? 'Publish Live Immediately' : 'వెంటనే లైవ్ చేయండి (Live Now)' }}</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; cursor: pointer; color: #1E40AF;">
                        <input type="radio" name="publish_mode" value="schedule" style="accent-color: #3B82F6; width: 16px; height: 16px;">
                        <span><i class="fa-solid fa-clock" style="color: #3B82F6;"></i> {{ app()->getLocale() === 'en' ? 'Schedule for Morning (5:00 AM IST)' : 'ఉదయం 5:00 AM కి షెడ్యూల్ చేయండి' }}</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; cursor: pointer; color: #475569;">
                        <input type="radio" name="publish_mode" value="draft" style="accent-color: #64748B; width: 16px; height: 16px;">
                        <span><i class="fa-solid fa-pencil"></i> {{ app()->getLocale() === 'en' ? 'Save as Draft' : 'డ్రాఫ్ట్‌గా ఉంచండి (Draft)' }}</span>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 14px; justify-content: flex-end; align-items: center; border-top: 1px solid var(--admin-border); padding-top: 20px;">
                <a href="{{ route('admin.editions.index') }}" class="btn-admin btn-outline">{{ __('admin.cancel') }}</a>
                <button type="submit" id="submitBtn" class="btn-admin btn-gold" style="font-size: 14px; padding: 11px 24px;">
                    <i class="fa-solid fa-check"></i> {{ app()->getLocale() === 'en' ? 'Upload & Process Pages' : 'అప్‌లోడ్ చేసి పేజీలను ఎడిట్ చేయండి' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Newsroom Staff Guidelines -->
    <div class="card" style="background: #F8FAFC; border-left: 4px solid var(--admin-gold);">
        <h4 style="font-size: 15px; font-weight: 700; color: var(--admin-navy); margin-bottom: 8px;">
            <i class="fa-solid fa-lightbulb" style="color: var(--admin-gold);"></i> {{ app()->getLocale() === 'en' ? 'Important Guidelines for Newsroom Staff:' : 'న్యూస్‌రూమ్ సిబ్బందికి ముఖ్య సూచనలు:' }}
        </h4>
        <ul style="font-size: 13px; color: var(--admin-muted); line-height: 1.8; margin-left: 20px;">
            <li>{{ app()->getLocale() === 'en' ? 'Original uploaded PDF is safely preserved in secure private storage.' : 'అప్‌లోడ్ చేసే పీడీఎఫ్ అసలైనది (Original) ప్రైవేట్ స్టోరేజ్‌లో భద్రపరచబడుతుంది.' }}</li>
            <li>{{ app()->getLocale() === 'en' ? 'Pages are automatically extracted and pre-rendered for the high-definition broadsheet reader.' : 'అప్‌లోడ్ పూర్తయిన తర్వాత ప్రతి పేజీ విడివిడిగా బ్రాడ్‌షీట్ రీడర్ కోసం సిద్ధమవుతుంది.' }}</li>
            <li>{{ app()->getLocale() === 'en' ? 'Review page sequences in Draft status before editorial approval.' : 'డ్రాఫ్ట్ స్టేటస్‌లో ఉన్నప్పుడు మీరు పేజీల వరుస క్రమాన్ని (Page Order) సరిచూసుకోవచ్చు.' }}</li>
            <li>{{ app()->getLocale() === 'en' ? 'Once approved, the edition is scheduled for publication at 5:00 AM IST daily.' : 'ఆమోదం పొందిన తర్వాత ప్రతిరోజూ ఉదయం 5:00 AM IST కి ఆటోమేటిక్‌గా లైవ్ చేయబడుతుంది.' }}</li>
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const fileInput = document.getElementById('pdf_file');
    const dropZone = document.getElementById('dropZone');
    const fileSelectedInfo = document.getElementById('fileSelectedInfo');
    const fileNameSpan = document.getElementById('fileName');
    const fileSizeSpan = document.getElementById('fileSize');
    const uploadForm = document.getElementById('uploadForm');
    const uploadProgressContainer = document.getElementById('uploadProgressContainer');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const submitBtn = document.getElementById('submitBtn');

    // Drag and Drop Events
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.borderColor = 'var(--admin-gold)';
            dropZone.style.background = '#FEFCE8';
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.borderColor = '#CBD5E1';
            dropZone.style.background = '#F8FAFC';
        });
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelection(files[0]);
        }
    });

    fileInput.addEventListener('change', function () {
        if (this.files.length > 0) {
            handleFileSelection(this.files[0]);
        }
    });

    function handleFileSelection(file) {
        if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
            alert('దయచేసి సరైన పీడీఎఫ్ (.pdf) ఫైల్‌ను మాత్రమే ఎంచుకోండి.');
            fileInput.value = '';
            fileSelectedInfo.style.display = 'none';
            return;
        }

        const sizeInMb = (file.size / (1024 * 1024)).toFixed(2);
        if (file.size > 100 * 1024 * 1024) {
            alert('ఫైల్ పరిమాణం 100MB కంటే ఎక్కువగా ఉంది. దయచేసి కంప్రెస్ చేసి అప్‌లోడ్ చేయండి.');
            fileInput.value = '';
            fileSelectedInfo.style.display = 'none';
            return;
        }

        fileNameSpan.textContent = file.name;
        fileSizeSpan.textContent = sizeInMb + ' MB';
        fileSelectedInfo.style.display = 'block';
    }

    // Progress Feedback simulation on form submission
    uploadForm.addEventListener('submit', function () {
        if (!fileInput.files || fileInput.files.length === 0) {
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> అప్‌లోడ్ అవుతోంది...';
        uploadProgressContainer.style.display = 'block';

        let progress = 10;
        progressBar.style.width = progress + '%';
        progressText.innerText = progress + '%';

        const interval = setInterval(() => {
            if (progress < 90) {
                progress += Math.floor(Math.random() * 12) + 5;
                if (progress > 90) progress = 90;
                progressBar.style.width = progress + '%';
                progressText.innerText = progress + '%';
            } else {
                clearInterval(interval);
            }
        }, 300);
    });
</script>
@endpush
