<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
$auth = new AuthController();
$auth->requireLogin();

$user = $_SESSION['user_name'] ?? 'User';
$pageTitle = 'Deputy Superintendent Approval - English';
include __DIR__ . '/../layouts/header.php';
?>
<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0 0 30px 0;
    color: #333;
}

/* Maroon Header Bar */
.top-navbar {
    background-color: #701515;
    color: #ffffff;
    padding: 12px 25px;
    font-size: 18px;
    font-weight: bold;
    text-align: left;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.application-container {
    max-width: 950px;
    background: #ffffff;
    margin: 20px auto;
    padding: 30px 40px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
    border-radius: 4px;
}

/* Header Actions Layout */
.header-action-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.back-btn-top {
    background-color: #ffc107;
    color: #000;
    font-weight: bold;
    border: 1px solid #d39e00;
    padding: 8px 22px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    transition: background-color 0.2s;
}

.back-btn-top:hover {
    background-color: #e0a800;
}

/* Office Use Box */
.office-use-box {
    border: 1px solid #000;
    padding: 10px 15px;
    text-align: center;
    background-color: #fff;
    min-width: 260px;
}

.office-title {
    font-weight: bold;
    font-size: 13px;
    margin-bottom: 8px;
}

.office-field {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
}

.form-input-date-sm {
    padding: 3px 6px;
    border: 1px solid #000;
    font-size: 12px;
}

/* Central Main Title */
.main-title-container {
    text-align: center;
    margin: 25px 0 30px 0;
    border-bottom: 2px solid #0f4c81;
    padding-bottom: 15px;
}

.main-title-container h1 {
    color: #0f4c81;
    font-size: 26px;
    margin: 0 0 8px 0;
    font-weight: bold;
}

.main-title-container h2 {
    color: #333;
    font-size: 18px;
    margin: 0;
    font-weight: bold;
}

.note-box {
    background-color: #fff3cd;
    border: 1px solid #ffeeba;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    font-size: 14px;
}

.section-title {
    font-weight: bold;
    font-size: 16px;
    margin-top: 25px;
    margin-bottom: 15px;
    color: #0f4c81;
    border-bottom: 1.5px solid #0f4c81;
    padding-bottom: 4px;
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.form-group {
    flex: 1;
    margin-bottom: 15px;
}

.form-group.full-width {
    width: 100%;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
    font-weight: 500;
}

.dotted-line {
    display: inline-block;
    border-bottom: 1px dotted #555;
    width: 100%;
    min-height: 20px;
}

/* Inputs, Textboxes, Dropdowns & Dates */
.dropdown-select, .form-input-date, .table-input, .form-input {
    width: 100%;
    padding: 10px 12px;  /* Textbox එක ඇතුළ ඉඩ වැඩි කිරීම සඳහා */
    border: 1px solid #b0b0b0;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
    background-color: #fff;
    margin-top: 5px;     /* Label එකයි textbox එකයි අතර පරතරය */
}

.table-input:focus, .dropdown-select:focus, .form-input-date:focus {
    border-color: #0f4c81;
    outline: none;
    box-shadow: 0 0 5px rgba(15, 76, 129, 0.2);
}
.inline-select-sm {
    width: auto;
    display: inline-block;
    padding: 4px 8px;
    margin: 0 4px;
}

.radio-group {
    display: flex;
    gap: 20px;
    margin-top: 5px;
}

.radio-label {
    font-weight: normal;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
}

.sub-section {
    margin-left: 20px;
    margin-top: 15px;
}

.indent {
    margin-left: 15px;
    margin-top: 10px;
}

.inline-select {
    max-width: 300px;
}

.custom-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.custom-table th, .custom-table td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: center;
    font-size: 13px;
}

.custom-table th {
    background-color: #f1f3f5;
}

.note-text {
    font-size: 12px;
    color: #d9534f;
    margin-top: 5px;
}

.nav-btn, .submit-btn {
    background-color: #0f4c81;
    color: white;
    border: none;
    padding: 9px 20px;
    font-size: 14px;
    border-radius: 4px;
    cursor: pointer;
}

.nav-btn:hover, .submit-btn:hover {
    background-color: #0b3961;
}

/* Dashboard Styles */
.dashboard-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 80vh;
    text-align: center;
}

.dashboard-card {
    background: #ffffff;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 500px;
    width: 100%;
}

.lang-btn {
    display: block;
    width: 100%;
    padding: 15px;
    margin: 15px 0;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
    box-sizing: border-box;
}

.sinhala-btn {
    background-color: #0f4c81;
    color: white;
}

.sinhala-btn:hover {
    background-color: #0b3961;
}

.english-btn {
    background-color: #28a745;
    color: white;
}

.english-btn:hover {
    background-color: #218838;
}

/* සාමාන්‍ය තිරයේදී අනුමත කිරීමේ කොටස නොපෙන්වයි */
#print-approval-section {
    display: none !important;
}

/* සාමාන්‍ය තිරයේදී විෂය ලිපිකරුගේ නිර්දේශිත කොටස නොපෙන්වයි */
.clerk-approval-section {
    display: none;
}

/* සාමාන්‍ය තිරයේදී අනුමත කිරීමේ සහ නිර්දේශ කිරීමේ කොටස් සම්පූර්ණයෙන්ම සැඟවේ */
.office-approval-section {
    display: none;
}


/* Print Styles for PDF generation */
@media print {
    .top-navbar, .back-btn-top, .nav-btn, .print-btn, button {
        display: none !important;
    }
    body {
        background-color: #fff;
        padding: 0;
    }
    .application-container {
        box-shadow: none;
        padding: 0;
        max-width: 100%;
    }
    .page-section {
        display: block !important;
        page-break-after: always;
    }
    #print-approval-section {
        display: block !important;
    }
    .button-container {
        display: none !important;
    }
    .clerk-approval-section {
        display: block !important;
    }
    body {
        background-color: white !important;
    }
    
    .office-approval-section {
        display: block !important;
    page-break-inside: avoid;
    }

    /* මුද්‍රණය කරන විට බොත්තම් සැඟවීම */
    button {
        display: none !important;
    }

    fieldset {
        border-color: #333 !important;
    }
    
}

</style>


    <!-- Top Maroon Navigation Bar -->
    <div class="w-full bg-[#701515] text-white py-3 px-6 text-lg font-bold shadow-md">
        Sri Lanka Railways - Railway Quarters Application Form
    </div>

    <!-- Main Container Card -->
    <div class="max-w-4xl mx-auto bg-white mt-6 p-8 shadow-md rounded-lg border border-gray-200">
        
        <!-- Header Actions: Back Button & Office Use Box -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <!-- Railway Yellow Back Button -->
                <a href="<?php echo baseUrl('dashboard'); ?>" class="inline-block bg-amber-400 hover:bg-amber-500 text-black font-bold py-2.5 px-5 rounded-md shadow transition duration-150 text-sm">
                &laquo; Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Title Section -->
        <div class="text-center border-b-2 border-[#0f4c81] pb-4 mb-6">
            <h1 class="text-2xl font-bold text-[#0f4c81] mb-1">Sri Lanka Railways</h1>
            <h2 class="text-lg font-semibold text-gray-700">Transportation Sub Department</h2>
        </div>

        <!-- Form Section Example (Fieldset with Tailwind) -->
        <form action="<?php echo baseUrl('approval/deputy/submit'); ?>" method="POST">
            <fieldset class="bg-white border border-gray-300 rounded-lg p-6 shadow-sm mb-6">
                <legend class="text-sm font-bold text-[#0f4c81] px-3 py-1 bg-gray-100 border border-gray-200 rounded-md">
                    Deputy Superintendent (Transportation) Colombo;
                </legend>

                <p class="text-sm font-medium text-gray-700 mt-4 mb-2">To the best of my knowledge,</p>
                
                <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside mb-6 leading-relaxed">
                    <li>The above-mentioned information is correct / corrected as appropriate,</li>
                    <li>The applicant / spouse or children do / do not possess residential property within 15 miles,</li>
                    <li>I hereby recommend / do not recommend this housing application of the applicant.</li>
                </ul>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Type and Number of Quarters Applied For:</label>
                    <input type="text" name="rec_quarter_type_no" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0f4c81] text-sm">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Essentiality to service (such as night shifts) and other remarks if any:</label>
                    <textarea name="rec_other_remarks" rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0f4c81] text-sm"></textarea>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-6 pt-4 border-t border-gray-100">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Date:</label>
                        <input type="date" name="rec_date" 
                            class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0f4c81] text-sm">
                    </div>
                    <div>
                        <button type="submit" class="bg-[#0f4c81] hover:bg-[#0b3961] text-white font-medium text-sm px-6 py-2.5 rounded-md shadow transition duration-150">
                            Approved
                        </button>
                    </div>
                </div>
            </fieldset>
        </form>

    </div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>