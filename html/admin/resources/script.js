function checkEverythingIsUploaded() {
	if (typeof $('button[title="Upload file"]:visible')[0] !== 'undefined')
	{
		console.log($('button[title="Upload file"]:visible'));
		alert("There are not finished files uploads");
		return false;
	}
	return true;
}

function goTab(num) {
	if (!checkEverythingIsUploaded())
		return;
	$(`#nav-home-${num}`).removeClass('disabled');
	$(`#nav-home-${num}`).click();
}

$('.nav-link:not(.logout,.dashboard)').on('click', function (e) {
	if (!checkEverythingIsUploaded())
		return;
	e.preventDefault()
	$(this).tab('show')
})

var fileinputMultiDefSettings = {
	theme: 'explorer-fas',
	uploadAsync: false,
	uploadUrl: "fileapi/api.php",
	deleteUrl: 'fileapi/delete.php',
	overwriteInitial: false,
	initialPreviewAsData: false,
	removeFromPreviewOnError: true,
	previewFileIcon: '<i class="fas fa-file"></i>',
	preferIconicPreview: true, // this will force thumbnails to display icons for following file extensions
	previewFileIconSettings: { // configure your icon file extensions
		'doc': '<i class="fas fa-file-word text-primary"></i>',
		'xls': '<i class="fas fa-file-excel text-success"></i>',
		'ppt': '<i class="fas fa-file-powerpoint text-danger"></i>',
		'pdf': '<i class="fas fa-file-pdf text-danger"></i>',
		'zip': '<i class="fas fa-file-archive text-muted"></i>',
		'htm': '<i class="fas fa-file-code text-info"></i>',
		'txt': '<i class="fas fa-file-alt text-info"></i>',
		'mov': '<i class="fas fa-file-video text-warning"></i>',
		'mp3': '<i class="fas fa-file-audio text-warning"></i>',
		// note for these file types below no extension determination logic 
		// has been configured (the keys itself will be used as extensions)
		'jpg': '<i class="fas fa-file-image text-danger"></i>', 
		'gif': '<i class="fas fa-file-image text-muted"></i>', 
		'png': '<i class="fas fa-file-image text-primary"></i>'    
	},
	previewFileExtSettings: { // configure the logic for determining icon file extensions
		'doc': function(ext) {
			return ext.match(/(doc|docx)$/i);
		},
		'xls': function(ext) {
			return ext.match(/(xls|xlsx)$/i);
		},
		'ppt': function(ext) {
			return ext.match(/(ppt|pptx)$/i);
		},
		'zip': function(ext) {
			return ext.match(/(zip|rar|tar|gzip|gz|7z)$/i);
		},
		'htm': function(ext) {
			return ext.match(/(htm|html)$/i);
		},
		'txt': function(ext) {
			return ext.match(/(txt|ini|csv|java|php|js|css)$/i);
		},
		'mov': function(ext) {
			return ext.match(/(avi|mpg|mkv|mov|mp4|3gp|webm|wmv)$/i);
		},
		'mp3': function(ext) {
			return ext.match(/(mp3|wav)$/i);
		}
	}
}

var fileinputSingleDefSettings = {
	theme: 'explorer-fas',
	showPreview: true,
	uploadAsync: false,
	uploadUrl: "fileapi/api.php",
	overwriteInitial: true,
	initialPreviewAsData: false,	
	removeFromPreviewOnError: true,
}
