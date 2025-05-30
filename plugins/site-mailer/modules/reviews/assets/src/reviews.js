import DirectionProvider from '@elementor/ui/DirectionProvider';
import { ThemeProvider } from '@elementor/ui/styles';
import domReady from '@wordpress/dom-ready';
import { StrictMode, Fragment, createRoot } from '@wordpress/element';
import ReviewsApp from './app';
import SettingsProvider from './hooks/use-settings';

domReady( () => {
	const rootNode = document.getElementById( 'reviews-app' );

	// Can't use the settings hook in the global scope so accessing directly
	const isDevelopment = window?.siteMailerSettingsData?.isDevelopment;
	const AppWrapper = Boolean( isDevelopment ) ? StrictMode : Fragment;

	const root = createRoot( rootNode );

	root.render(
		<AppWrapper>
			<DirectionProvider rtl={ false }>
				<ThemeProvider colorScheme="light">
					<SettingsProvider>
						<ReviewsApp />
					</SettingsProvider>
				</ThemeProvider>
			</DirectionProvider>
		</AppWrapper>,
	);
} );
