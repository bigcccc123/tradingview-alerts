/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

const Dashboard = () => {
    return (
        <div className="dashboard">
            <div className="card p-5">
                <h3 className="font-medium text-lg">
                    {__('Dashboard', 'tradingview_alerts')}
                </h3>
                <p>
                    {__('Edit Dashboard component at ', 'tradingview_alerts')}
                    <code>src/components/Dashboard.jsx</code>
                </p>
            </div>
        </div>
    );
};

export default Dashboard;
