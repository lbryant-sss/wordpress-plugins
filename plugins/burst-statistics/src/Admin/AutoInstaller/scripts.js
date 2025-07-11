const burst_steps = burst_autoinstaller.steps;
let burst_download_link = '';
let burst_progress = 0;

//set up steps html
let burst_template = document.getElementById( 'burst-step-template' ).innerHTML;
let burst_start_button = document.getElementById( 'burst-start-button' ).innerHTML;
let burst_total_step_html = '';
burst_steps.forEach( ( step, i ) =>	{
	let stepHtml = burst_template;
	stepHtml = stepHtml.replace( '{doing}', step.doing );
	stepHtml = stepHtml.replace( '{step}', 'burst-step-' + i );
	burst_total_step_html += stepHtml;
});
document.querySelector( '.burst-install-steps' ).innerHTML = burst_total_step_html + burst_start_button;

const sanitizeRestAction = (action) => {
	const allowedActions = [
		'destination_clear',
		'activate_license',
		'package_information',
		'install_plugin',
		'activate_plugin',
	];

	return allowedActions.includes(action) ? action : '';
}
const ajaxRequest = async( path, requestData = null ) => {
	const url = burst_autoinstaller.admin_ajax_url+`&rest_action=${path.replace('?', '&')}`;
	const options = {
		method: 'GET',
		headers: {'Content-Type': 'application/json; charset=UTF-8'}
	};

	try {
		// the path consists of sanitized actions. Url is hardcoded in localize scripts.
		const response = await fetch(url, options); // nosemgrep
		if (!response.ok) {
			console.log(false, response.statusText);
			throw new Error(response.statusText);
		}

		const responseData = await response.json();

		if (
			!responseData.data ||
			!Object.prototype.hasOwnProperty.call(responseData.data, 'request_success')
		) {
			throw new Error('Invalid data error');
		}

		delete responseData.data.request_success;

		// return promise with the data object
		return Promise.resolve(responseData.data);
	} catch (error) {
		return Promise.reject(new Error('AJAX request failed'));
	}
}
const burst_set_progress = () => {
	if ( 100 <= burst_progress ) {
		burst_progress = 100;
	}
	let progress_bar_container = document.querySelector( '.burst-progress-bar-container' );
	let progressEl = progress_bar_container.querySelector( '.burst-progress' );
	let bar = progressEl.querySelector( '.burst-bar' );
	bar.style = 'width: ' + burst_progress + '%;';

	if ( 100 === burst_progress ) {
		clearInterval( window.rsp_interval );
	}
};

const burst_stop_progress = () => {
	clearInterval( window.rsp_interval );
	let progress_bar_container = document.querySelector( '.burst-progress-bar-container' );

	let progressEl = progress_bar_container.querySelector( '.burst-progress' );
	let bar = progressEl.querySelector( '.burst-bar' );
	bar.style = 'width: 100%;';
	bar.classList.remove( 'burst-green' );
	bar.classList.add( 'burst-red' );
	clearInterval( window.rsp_interval );
};

const glue = ( path ) => {
	path = burst_autoinstaller.rest_url + path;
	return path.indexOf( '?' ) === -1 ? '?' : '&';
};

const burst_process_response = ( response, current_step, step, progress_step ) => {
	response = response.data;
	const step_element = document.querySelector( '.burst-step-' + current_step );
	if ( ! step_element ) {
		return;
	}

	const step_color = step_element.querySelector( '.burst-step-color' );
	const step_text = step_element.querySelector( '.burst-step-text' );

	if ( response.success ) {
		if ( response.download_link ) {
			burst_download_link = response.download_link;
		}
		step_color.innerHTML = '<div class="burst-green burst-bullet"></div>';
		step_text.innerHTML = '<span>' + step.success + '</span>';

		if ( current_step + 1 === burst_steps.length ) {
			let templateHtml = document.getElementById( 'burst-plugin-suggestion-template' ).innerHTML;
			document.querySelector( '.burst-install-steps' ).innerHTML = templateHtml;
			document.querySelector( '.burst-install-plugin-modal h3' ).innerText = burst_autoinstaller.finished_title;
			document.querySelector( '.burst-btn.burst-visit-dashboard' ).classList.remove( 'burst-hidden' );
			document.querySelector('.burst-running').style.display = 'none';
			document.querySelector('.burst-done').style.display = 'block';

			burst_progress = 100;
			burst_set_progress();
		} else {
			burst_progress = progress_step;
			burst_set_progress();
			burst_process_step( current_step + 1 );
		}
	} else {
		step_color.innerHTML = '<div class="burst-red burst-bullet"></div>';
		if ( response.message ) {
			document.querySelector( '.burst-error-message.burst-' + step.type + ' span' ).innerText = response.message;
		}
		step_text.innerHTML = '<span>' + step.error + '</span>';
		burst_stop_progress();
		document.querySelector( '.burst-btn.burst-cancel' ).classList.remove( 'burst-hidden' );
		document.querySelector( '.burst-error-message.burst-' + step.type ).classList.remove( 'burst-hidden' );
	}
}

const burst_process_step = async ( current_step ) => {
	let progress_step = ( current_step + 1 ) * Math.ceil( 100 / ( burst_autoinstaller.steps.length ) );

	clearInterval( window.rsp_interval );
	window.rsp_interval = setInterval( function() {
		let inc = 0.5;
		//very slow if we're close to the target progress for this step.
		if ( ( burst_progress > progress_step - 1 ) ) {
			inc = 0.01;
		}

		burst_progress += inc;
		if ( 100 <= burst_progress ) {
			burst_progress = 100;
		}
		burst_set_progress();
	}, 100 );

	current_step = parseInt( current_step );
	let step = burst_steps[current_step];

	// Get arguments from url
	const query_string = window.location.search;
	const urlParams = new URLSearchParams( query_string );
	const action = sanitizeRestAction( step.action );
	let data = {
		'path': action,
		'token': burst_autoinstaller.token,
		'plugin': urlParams.get( 'plugin' ),
		'license': urlParams.get( 'license' ),
		'item_id': urlParams.get( 'item_id' ),
		'download_link': burst_download_link,
		'install_pro': true
	};
	const queryString = new URLSearchParams(data).toString();
	const path = `/burst/v1/auto_installer/${action}${glue()}${queryString}`;

	await wp.apiFetch( {
		path: path,
		method: 'GET',
	} ).then( ( response ) => {
		burst_process_response( response, current_step, step, progress_step );
	} ).catch( ( error ) => {

			//try with admin-ajax
			ajaxRequest( path, data ).then( ( response ) => {
				burst_process_response( response, current_step , step, progress_step);
			})
	});
};
document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('.burst-start').forEach(function (button) {
		button.addEventListener('click', function () {
			document.querySelectorAll('.burst-start').forEach(function (button) {
				button.disabled = true;
			});
			document.querySelector('.burst-initial').style.display = 'none';
			document.querySelector('.burst-running').style.display = 'block';
			burst_process_step(0);
		});
	});
});



