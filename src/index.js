/**
 * External dependencies
 */
import { render } from '@wordpress/element';

/**
 * Internal dependencies
 */
import App from './App';
//import * as serviceWorker from './serviceWorker';

// Import the stylesheet for the plugin.
import './style/tailwind.css';
import './style/main.scss';

// Render the App component into the DOM
const alertPlaceElement = document.getElementById( 'tradingview_alerts' );

if (alertPlaceElement) {
	render( < App / > , alertPlaceElement );
}

// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: https://bit.ly/CRA-PWA
//serviceWorker.register();
