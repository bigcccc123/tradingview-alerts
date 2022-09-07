/**
 * Internal dependencies
 */
import AlertsPage from '../pages/AlertsPage';
import OrdersPage from '../pages/OrdersPage';

const routes = [
	{
		path: '/',
		element: AlertsPage,
	},
	{
		path: '/orders',
		element: OrdersPage,
	}
	];

	export default routes;
