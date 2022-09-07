/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {Routes, Route, useNavigate} from 'react-router-dom';
import Table, { AvatarCell, SelectColumnFilter, StatusPill } from './Table';

const OrdersPage = () => {
    const navigate = useNavigate();

    const url = wpApiSettings.root + 'tradingview-orders/v1/orders';
    const [orders, setOrders] = React.useState([]);

    React.useEffect(() => {
        async function loadOrders() {
            const response = await fetch(url, {
                headers : {
                    'X-WP-Nonce' : wpApiSettings.nonce
                }
            });
            if(!response.ok) {
                // oups! something went wrong
                console.log("something wrong");
                return;
            }
    
            const orders = await response.json();
            setOrders(orders);
        }
    
        loadOrders();
    }, []);

    const columns = React.useMemo(() => [
        
      ], []);

    return (
        
        <div className="dashboard">
            <div className="min-h-screen bg-gray-100 text-gray-900">
                <main className="mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div className="">
                    <h3 className="text-xl font-semibold">Orders</h3>
                    </div>

                    <div className="mt-6">
                        Comming Soon
                    </div>
                </main>
            </div>
            
        </div>
    );
};

export default OrdersPage;
