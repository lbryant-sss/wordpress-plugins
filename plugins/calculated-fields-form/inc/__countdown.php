<?php
$currentTime = new DateTime();
$targetTime = new DateTime('2025-10-31 23:59');
if ($currentTime < $targetTime):
?>
<style>
	.countdown{text-align:center;width:100%;}
	.countdown-container {
		display: flex;
		gap: 0.4rem;
		justify-content: center;
		align-items: center;
		flex-wrap: wrap;
		padding: 0 1rem;
	}

	.countdown-title { display:block; text-align: center;}
	.countdown-time-unit {
		display: flex;
		flex-direction: column;
		align-items: center;
	}

	.countdown-time-box {
		width: 3rem;
		height: 3rem;
		background: white;
		border: 1px solid #cbd5e1;
		border-radius: 0.5rem;
		display: flex;
		align-items: center;
		justify-content: center;
		margin-bottom: 0;
	}

	.countdown-time-value {
		font-size: 1.5rem;
		font-weight: 700;
		color: #1e293b;
	}

	.countdown-time-label {
		font-size: 0.5rem;
		font-weight: 600;
		color: #64748b;
		text-transform: uppercase;
		letter-spacing: 0.1em;
	}

	.countdown-separator {
		font-size: 1.875rem;
		font-weight: 300;
		color: #cbd5e1;
		margin-bottom: 0.5rem;
	}

	@media (max-width: 640px) {
		.countdown-time-box {
			width: 5rem;
			height: 5rem;
		}

		.countdown-time-value {
			font-size: 1.875rem;
		}

		.countdown-container {
			gap: 1rem;
		}
	}
</style>
<div class="countdown">
	<h2 class="countdown-title" style="margin-top:10px;margin-bottom:10px;clear:both;">Halloween Offer <a href="https://cff.dwbooster.com/download" target="_blank" style="font-size:1.3em;text-decoration:none;">40% OFF</a></h2>
	<div class="countdown-container">
		<div class="countdown-time-unit">
			<div class="countdown-time-box">
				<span class="countdown-time-value countdown_days">00</span>
			</div>
			<span class="countdown-time-label">Days</span>
		</div>

		<div class="countdown-separator">:</div>

		<div class="countdown-time-unit">
			<div class="countdown-time-box">
				<span class="countdown-time-value countdown_hours">00</span>
			</div>
			<span class="countdown-time-label">Hours</span>
		</div>

		<div class="countdown-separator">:</div>

		<div class="countdown-time-unit">
			<div class="countdown-time-box">
				<span class="countdown-time-value countdown_minutes">00</span>
			</div>
			<span class="countdown-time-label">Minutes</span>
		</div>

		<div class="countdown-separator">:</div>

		<div class="countdown-time-unit">
			<div class="countdown-time-box">
				<span class="countdown-time-value countdown_seconds">00</span>
			</div>
			<span class="countdown-time-label">Seconds</span>
		</div>
	</div>
</div>

<script>
	function closead_popup() {
		// document.querySelector('.ad-overlay').style.display = 'none';
	}
	if ( typeof updateCountdown == 'undefined' ) {
		function updateCountdown() {
			const targetDate = new Date('2025-10-31T23:59:59').getTime();
			const now = new Date().getTime();
			const difference = targetDate - now;

			const countdownEls = document.getElementsByClassName('countdown');
			for( let i = 0, h = countdownEls.length; i < h; i++ ) {
				const countdownEl = countdownEls[i];
				if (difference > 0) {
					countdownEl.style.display="block";
					const days = Math.floor(difference / (1000 * 60 * 60 * 24));
					const hours = Math.floor((difference / (1000 * 60 * 60)) % 24);
					const minutes = Math.floor((difference / 1000 / 60) % 60);
					const seconds = Math.floor((difference / 1000) % 60);

					countdownEl.querySelector('.countdown_days').textContent = String(days).padStart(2, '0');
					countdownEl.querySelector('.countdown_hours').textContent = String(hours).padStart(2, '0');
					countdownEl.querySelector('.countdown_minutes').textContent = String(minutes).padStart(2, '0');
					countdownEl.querySelector('.countdown_seconds').textContent = String(seconds).padStart(2, '0');

				} else {
					countdownEl.style.display="none";
				}
			}
		}

		updateCountdown();
		setInterval(updateCountdown, 1000);
	}

</script>
<?php
endif;