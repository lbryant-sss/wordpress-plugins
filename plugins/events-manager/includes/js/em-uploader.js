/*!
 * EM Uploader 1.0
 * Copyright 2025, Pixelite SL
 * Licensed under GPLv2, https://opensource.org/license/gpl-2-0
 * Please visit https://wp-events-plugin.com for details.
 */

/*
This is an implementation of FilePond for use in Events Manager. Detects input fields with em-uploader classes and converts them to FilePond upload UIs.

Some of the extra things added;

* Restore feature for forms submitted without AJAX and reload with previously unsubmitted files.
* Inline previously submitted files display by adding a <script type="application/json" class="em-uploader-files"> element containing JSON object of files.
* Direct loading from the URL for previously loaded files such as public images, along with the ability to load restricted files via API load.
* Inline override by adding a <script type="application/json" class="em-uploader-options"> element containing JSON object of options that will overwrite default em-uploader.js filepond options
* Custom thumbnail plugin showing a small thumbnail rather than covering the whole file item in the UI as a background
* preventing forms from submitting whilst an upload is happening
* custom errors returned by the API
* download file and preview buttons are in different locations for better UI experience, via the FilePondPluginGetFile and FilePondPluginImageOverlay buttons
* filename preservation between page loads and form submissions, submitted via additional hidden fields alongside file ids on server
* Support for fallback methods, removing items wrapped in em-input-upload-fallback class after successful filepond loading
* Overlay preview, image and file size/type validation provided via FilePond official plugins.

*/


document.addEventListener('em_uploader_ready', function(e) {
	// get script options
	let script = document.getElementById('filepond-js');
	// add locale
	if ( script && script.dataset.locale ) {
		let module = "../external/filepond/locale/" + script.dataset.locale + ".js";
		import( module ).then((lang) => {
			if ( lang.default ) {
				FilePond.setOptions(lang.default);
			}
		}).catch( e => { console.log('Error loading locale : %o', e ); } );
	}

	// add a hidden input with a postfix name, which will add the fileId as a key
	let getHiddenInputName = function( input, fileId, postFix ) {
		let name;
		if ( input.dataset.fieldId && input.name.includes(`[${input.dataset.fieldId}]`)) {
			// field ID means we're in an array context, and id is at end, so we must copy/replace that part
			name = input.name.replace(`[${input.dataset.fieldId}]`, `[${input.dataset.fieldId}--${postFix}][${fileId}]`);
		} else {
			name = input.name.replace(/^([^[]+)(\[[\s\S]*)?$/, `$1--${postFix}$2`) + `[${fileId}]`;
		}
		return name.replace(/\[\]/, '');
	}

	let filenames = {};
	let sources = {};

	let setup_em_uploader = function( container ) {
		container.querySelectorAll('input.em-uploader').forEach(input => {
			let input_data = {};
			let wrapper = input.closest('.em-input-upload') ?? input.parentElement;
			let pond;

			// filepond only
			if (script) {
				// add plugins, we can assume there's at least one upload element on this page instance
				FilePond.registerPlugin(
					FilePondPluginFileValidateType,
					FilePondPluginFileValidateSize,
					FilePondPluginImageExifOrientation,
					FilePondPluginImageValidateSize,
					FilePondPluginGetFile,
					FilePondPluginImageOverlay,
					// in-house add-ons
					FilePondPluginImageThumbnail,
					FilePondPluginPdfPreviewOverlay,
					FilePondPluginFileIcon
				);
				// Loop through and initialize FilePond
				const apiURL = new URL(EM.uploads.endpoint);
				apiURL.searchParams.set('path', input.dataset.apiPath || '');
				apiURL.searchParams.set('path_id', input.dataset.apiPathId || null);
				apiURL.searchParams.set('field_id', input.dataset.fieldId || '');
				let apiNonce = input.dataset.apiNonce ? input.dataset.apiNonce : null;

				// check if there's a file already added by looking for their neighbor
				let filesOptions = wrapper.querySelector('.em-uploader-files');
				let files = [];
				if (filesOptions) {
					let files_data = JSON.parse(filesOptions.text);
					if (files_data.length > 0) {
						files_data.forEach((file) => {
							let opt;
							if ('url' in file) {
								opt = {
									source: file.url,
									type: 'local',
									options: {
										metadata: {
											id: file.id,
											previouslyUploaded: true,
										},
									}
								};
								if ( !file.deleted ) {
									opt.options.type = 'local';
								}
							} else {
								opt = {
									source: file.id,
									options: {
										type: 'limbo',
										metadata: {
											id: file.id,
										},
									}
								};
							}
							if ('name' in file) {
								opt.options.metadata.filename = file.name;
							}
							file.opt = opt;
							input_data[file.id] = file;
							if ( !file.deleted ) {
								files.push(opt);
							}
						});
					}
				}

				// init options
				let pondOptions = {
					files: files,
					// Configure FilePond options here
					allowMultiple: input.multiple,  // Allow multiple file uploads
					credits: false, // Remove "Powered by PQINA"
					server: {
						process: {
							url: apiURL.toString(),
							credentials: 'same-origin',
							method: 'POST',
							headers: {
								'X-WP-Nonce': EM.api_nonce, // Include the nonce in the request headers
								'X-EM-Nonce': apiNonce,
							},
							onload: (response) => {
								// Parse the JSON response from the server
								const data = JSON.parse(response);

								if (data.success && data.file && data.file.id) {
									// Store data in input_data, indexed by file ID
									input_data[data.file.id] = {
										name: data.file.name,
										size: data.file.size,
										type: data.file.type,
										nonce: data.nonce, // nonce to manipulate data
									};
									// add a delete input
									const hiddenInput = document.createElement('input');
									hiddenInput.type = 'hidden';
									hiddenInput.name = getHiddenInputName(input, data.file.id, 'names');
									hiddenInput.value = data.file.name;
									hiddenInput.className = input.name.replace(/\[.*$/, '') + '-' + data.file.id;
									wrapper.appendChild(hiddenInput);
								}
								// Return the file ID to be used internally by FilePond
								wrapper.closest('form').onsubmit = null;
								return data.file.id;
							},
							onerror: (response) => response,
							withCredentials: true, // Include cookies if needed for authentication
						},
						revert: (uniqueFileId, load, error) => {
							// If it's an ID, construct the load endpoint and fetch
							if (uniqueFileId in input_data) {
								const url = new URL(apiURL.href); // add file id to path so we know it's a revert
								url.searchParams.set('tmp_file', uniqueFileId);
								url.searchParams.set('nonce', input_data[uniqueFileId].nonce);
								fetch(url, {
									method: 'DELETE',
									credentials: 'same-origin',
									headers: {
										'X-WP-Nonce': EM.api_nonce,
										'X-EM-Nonce': apiNonce,
									}
								}).then((response) => {
									if (response.ok) {
										return response.blob();
									}
									throw new Error('Failed to fetch file by ID.');
								}).then(load).catch((err) => {
									console.error(err);
									error(err.message);
								});
							}
						},
						load: (source, load, error) => {
							// Check if the source is a valid URL or an ID
							let err = (err) => {
								console.log(err);
								error(err.message);
							};
							if (source.startsWith('http://') || source.startsWith('https://')) {
								// URL - fetch the contents
								// blob loading function so we can inject a filename for preview/dl functionality
								let loadBlob = (blob) => {
									if (filenames[source]) {
										blob.name = filenames[source];
									}
									return load(blob);
								}
								// load cached promise if defined already
								if (sources[source]) {
									// source is already being fetched, attach a new then() to it
									sources[source].then(loadBlob).catch(err);
									return;
								}
								// Create and cache a promise that resolves to the blob
								sources[source] = fetch(source)
									.then(response => {
										if (response.ok) {
											const disposition = response.headers.get('Content-Disposition');
											if (disposition && disposition.indexOf('filename=') !== -1) {
												// Use a regular expression to extract the filename value
												const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
												const matches = filenameRegex.exec(disposition);
												if (matches !== null && matches[1]) {
													// Remove any surrounding quotes from the filename
													filenames[source] = matches[1].replace(/['"]/g, '');
												}
											}
											return response.blob();
										}
										throw new Error('Failed to fetch file from URL.');
									});
								// Attach load and error handling to the promise
								sources[source].then(loadBlob).catch(err);
							} else if (source in input_data) {
								// ID - construct the load endpoint and fetch
								let url = new URL(apiUrl); // clone and edit
								url.searchParams.set('file_id', source);
								url.searchParams.set('nonce', input_data[source].nonce);
								fetch(url, {
									method: 'GET',
									credentials: 'same-origin',
									headers: {
										'X-WP-Nonce': EM.api_nonce,
										'X-EM-Nonce': apiNonce,
									},
								}).then((response) => {
									if (response.ok) {
										return response.blob();
									}
									throw new Error('Failed to fetch file by ID.');
								}).then(load).catch(err);
							}
						},
						/* Possible feature, to delete known files remotely via AJAX rather than via uploading file.
						remove: ( source, load, error ) => {
							// get the ID of the source if it's not alphanumeric
							const isUrl = source.startsWith('http://') || source.startsWith('https://');
							let sourceId;
							if ( isUrl ) {
								// get hashtag
								let sourceParts = source.split('#');
								if( sourceParts.length > 1 ) {
									sourceId = sourceParts[1];
								}
							} else {
								sourceId = source;
							}
							if ( sourceId in input_data ) {
								let url = new URL(apiUrl); // clone and edit
								url.searchParams.set('file_id', sourceId);
								url.searchParams.set('nonce', input_data[sourceId].nonce);
								fetch( url, {
									method: 'DELETE',
									credentials: 'same-origin',
									headers: {
										'X-WP-Nonce': EM.api_nonce,
									},
								}).then((response) => {
									if (!response.ok) {
										error('Failed to fetch file by ID.');
									}
								}).catch((err) => {
									console.error(err);
									error(err.message);
								});
							} else {
								if ( sourceId ) error('No ID provided.');
								if ( apiNonce ) error('No EM API Nonce provided.');
							}
							load();
						},
						 */
						remove: null,
						restore: {
							url: apiURL.toString() + "&temp_id=", //file_id is added to end already
							credentials: 'same-origin',
							headers: {
								'X-WP-Nonce': EM.api_nonce, // Include the nonce in the request headers
								'X-EM-Nonce': apiNonce,
								'X-Filenames': JSON.stringify(input_data),
							},
						},
						fetch: null,
					},
					beforeRemoveFile: (item) => {
						let metaData = item.getMetadata();
						if ('previouslyUploaded' in metaData && metaData.previouslyUploaded) {
							// add a delete input
							moveToTBD( metaData.id );
						} else {
							// remove hidden input if exists for filename
							if (typeof item.serverId === 'string') {
								wrapper.querySelectorAll('input.' + input.name.replace(/\[.*$/, '') + '-' + item.serverId).forEach((input) => input.remove());
							}
						}

						return true;
					},
					name: 'filepond',
					allowDownloadByUrl: true,
					// thubmnail
					allowImageThumbnail: true,
					imagePreviewHeight: 100,
					// image size validation
					allowImageValidateSize: true,
					imageValidateSizeMinWidth: EM.uploads.images.image_min_width || 0,
					imageValidateSizeMaxWidth: EM.uploads.images.image_max_width || 6144,
					imageValidateSizeMinHeight: EM.uploads.images.image_min_height || 0,
					imageValidateSizeMaxHeight: EM.uploads.images.image_max_height || 6144,
					// file validation
					allowFileSizeValidation: true,
					maxFileSize: EM.uploads.files.max_file_size || null,
					// file type validation
					allowFileTypeValidation: true,
					acceptedFileTypes: EM.uploads.files.types,
					// file icons
					allowFileIcon: true,
					fileIconIncludeImages: false,

					// allows for server-side errors
					labelFileProcessingError: (error) => {
						try {
							let errorData = JSON.parse(error.body);
							return errorData.error || 'Upload failed.';
						} catch (e) {
							if (error.body) { // text/plain return
								return error.body;
							}
						}
						return 'Error during upload';
					},
				};

				// pre-override cleanup
				if ( input.accept ) { // let accept override, unless inline options override
					delete pondOptions.acceptedFileTypes;
				}

				// check if there's an options holder
				let inline_options = wrapper.querySelector('.em-uploader-options');
				if (inline_options) {
					Object.assign(pondOptions, JSON.parse(inline_options.text));
				}

				// last cleanup
				if ( typeof pondOptions.acceptedFileTypes === 'undefined' || pondOptions.acceptedFileTypes.length === 0 ) {
					pondOptions.allowFileTypeValidation = !!input.accept;

				}

				// start it up!
				pond = FilePond.create(input, pondOptions);

				// prevent form from processing whilst uploading files
				let form = wrapper.closest('form');
				let offForm = (e) => {
					if (e.origin === 1) {
						form.onsubmit = e => false;
						form.disabled = true;
						form.querySelectorAll('input[type="submit"],button[type="submit"]').forEach(el => {
							el.disabled = true
						});
					} else if (e.origin === 3) {
						// add correct filename to pre-load instaed of endpoint
						const file = files.find(file => file.options.type === 'local' && file.source === e.serverId);
						if (file) {
							file.id = e.id; // for later use
							const li = wrapper.querySelector(`li.filepond--item#filepond--item-${e.id}`);
							if (li) {
								const info = li.querySelector(".filepond--file-info-main");
								if (info.firstChild && info.firstChild.nodeType === Node.TEXT_NODE) {
									info.firstChild.nodeValue = file.options.metadata.filename;
								} else {
									info.textContent = file.options.metadata.filename;
								}
							}
						}
					}
				};
				pond.on('initfile', offForm);
				pond.on('processfilestart', offForm);
				let onForm = ( error = null, file = null ) => {
					form.onsubmit = null;
					form.disabled = false;
					form.querySelectorAll('input[type="submit"],button[type="submit"]').forEach(el => {
						el.disabled = false
					});
					if ( error && file ) {
						file.setMetadata('serverId', null);
					}
				};
				pond.on('processfiles', onForm);
				pond.on('processfile', onForm);
				pond.on('addfile', (error, file) => {
					if (error) {
						file.setMetadata('id', null); // This prevents it from being submitted
						file.setMetadata('serverId', null); // This prevents it from being submitted
					}
				});
				/*
				// rejig local filenames, in case we're loading via an ID but not the actual filename for security
				// TODO: remove this if nobody experiences issue, as now we are adding it to teh blob during fetch of a url
				pond.on('addfile', () => {
					files.forEach( file => {
						if ( file.options.type !== 'local' ) return;
						const li = wrapper.querySelector(`li.filepond--item#filepond--item-${file.id}`);
						if (li) {
							const info = li.querySelector(".filepond--file-info-main");
							if (info.firstChild && info.firstChild.nodeType === Node.TEXT_NODE) {
								info.firstChild.nodeValue = file.options.metadata.filename;
							} else {
								info.textContent = file.options.metadata.filename;
							}
						}
					});
				});
				*/

				// remove fallback stuff
				wrapper.querySelectorAll('.em-input-upload-fallback').forEach(el => {
					el.classList.add('hidden');
				});
			}
			// add fallback JS, which works alongside filePond for undoing deleted files
			const tbdList = wrapper.querySelector('.em-input-upload-files-tbd');
			const uploadList = wrapper.querySelector('.em-input-upload-files');

			const updateListVisibility = () => {
				if ( tbdList ) {
					const tbdHasFiles = tbdList.querySelectorAll('li[data-file_id]').length > 0;
					tbdList.classList.toggle('hidden', !tbdHasFiles);
				}

				if ( uploadList ) {
					const uploadedHasFiles = uploadList.querySelectorAll('li[data-file_id]').length > 0;
					uploadList.classList.toggle('hidden', !uploadedHasFiles);
				}
			};

			const checkUploadMaximums = () => {
				if ( !script ) {
					// if not using FilePond check if uplodaed files exceed permitted maximum, if so disable the uploader input
				}
			};

			const getHiddenDeleteInput = (fileId) =>
				wrapper.querySelector(`input[type="hidden"][data-file_id="${fileId}"]`);

			const addDeleteInput = (fileId) => {
				if (getHiddenDeleteInput(fileId)) return;

				const hiddenInput = document.createElement('input');
				hiddenInput.type = 'hidden';
				hiddenInput.name = getHiddenInputName(input, fileId, 'deleted');
				hiddenInput.value = '1';
				hiddenInput.dataset.file_id = fileId;
				wrapper.appendChild(hiddenInput);
			};

			const removeDeleteInput = (fileId) => {
				const existingInput = getHiddenDeleteInput(fileId);
				if (existingInput) existingInput.remove();
			};

			const moveToTBD = ( fileOrId ) => {
				let fileItem;
				if ( typeof fileOrId === 'string' ) {
					fileItem = wrapper.querySelector(`.em-input-upload-files li[data-file_id="${fileOrId}"]`)
				} else {
					fileItem = fileOrId;
				}
				if ( fileItem ) {
					if ( tbdList ) {
						tbdList.appendChild(fileItem);
					}
					const fileId = fileItem.dataset.file_id;
					addDeleteInput(fileId);
					updateListVisibility();
					updateInputVisibility();
				}
			};

			const moveToUploads = ( fileOrId ) => {
				let fileItem;
				if ( typeof fileOrId === 'string' ) {
					fileItem = wrapper.querySelector(`.em-input-upload-files-tbd li[data-file_id="${fileOrId}"]`)
				} else {
					fileItem = fileOrId;
				}
				if ( fileItem ) {
					const fileId = fileItem.dataset.file_id;
					const maxFiles = parseInt(input.dataset.maxFiles, 10) || (input.multiple ? null : 1);
					if (maxFiles) {
						const uploadedCount = getUploadedFilesCount();
						const pendingCount = getPendingUploadCount();
						const totalFiles = uploadedCount + pendingCount + 1 ; // add the one we're restoring

						if (totalFiles > maxFiles) {
							alert(`You cannot restore this file as it would exceed the maximum limit of ${maxFiles} files.`);
							return;
						}
					}

					if (script && pond && input_data[fileId]) {
						let file = input_data[fileId];

						// Restore into FilePond
						file.opt.options.type = 'local';
						pond.addFile( file.opt.source, file.opt.options ).then(() => {
							if ( uploadList ) {
								uploadList.appendChild(fileItem);
							}
							removeDeleteInput(fileId);
							updateListVisibility();
							updateInputVisibility();
						}).catch(() => {
							alert(`Failed to restore file: ${input_data[fileId].name}`);
						});
					} else {
						removeDeleteInput(fileId);
						updateListVisibility();
						updateInputVisibility();
						if ( uploadList ) {
							uploadList.appendChild(fileItem);
						}
					}

				}
			};


			wrapper.addEventListener('click', (e) => {
				if (e.target.matches('.em-icon-trash')) {
					const fileItem = e.target.closest('li[data-file_id]');
					moveToTBD(fileItem);
				}

				if (e.target.matches('.em-icon-undo')) {
					const fileItem = e.target.closest('li[data-file_id]');
					moveToUploads(fileItem);
				}
			});

			// Initial visibility check in case some files are preloaded in tbd state
			updateListVisibility();

			// check max uploads
			const getUploadedFilesCount = () => uploadList ? uploadList.querySelectorAll('li[data-file_id]').length : 0;

			const getPendingUploadCount = () => input.files.length;

			const updateInputVisibility = () => {
				const maxFiles = parseInt(input.dataset.maxFiles, 10) || (input.multiple ? null : 1);
				if (!maxFiles) return;

				const uploadedCount = getUploadedFilesCount();
				const pendingCount = getPendingUploadCount();
				const totalFiles = uploadedCount + pendingCount;

				if ( totalFiles >= maxFiles && pendingCount === 0 ) {
					input.classList.add('hidden');
				} else {
					input.classList.remove('hidden');
				}
			};

			let lastValidFileSelection;

			const validateMaxFilesBeforeUpload = (e) => {
				if ( script ) return; // omit this check if using FilePond
				const maxFiles = parseInt(input.dataset.maxFiles, 10) || (input.multiple ? null : 1);
				if (!maxFiles) return;

				const uploadedCount = getUploadedFilesCount();
				const selectedFilesCount = e.target.files.length;

				const totalFiles = uploadedCount + selectedFilesCount;

				if (totalFiles > maxFiles) {
					alert(`You can only upload a maximum of ${maxFiles} files.`);
					if ( lastValidFileSelection ) {
						input.files = lastValidFileSelection.files;
					} else {
						input.value = '';
					}
				} else {
					lastValidFileSelection = new DataTransfer();
					for (const file of input.files) {
						lastValidFileSelection.items.add(file);
					}
				}
				updateInputVisibility();
			};
			updateInputVisibility();

			input.addEventListener('change', validateMaxFilesBeforeUpload);
		});
	}
	setup_em_uploader( document ); // init
	// load in dynamic content
	document.addEventListener('em_setup_ui_elements', ( e ) => {
		setup_em_uploader( e.detail.container );
	});
})