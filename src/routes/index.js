/**
 * Internal dependencies
 */
import HomePage from '../pages/HomePage';
import AlertCreate from '../pages/AlertCreate';
import AlertsPage from '../pages/AlertsPage';

const routes = [
	{
		path: '/',
		element: HomePage,
	},
	{
		path: '/alerts',
		element: AlertsPage,
	},
	{
		path: '/jobs-create',
		element: AlertCreate,
	}
	];

	export default routes;
