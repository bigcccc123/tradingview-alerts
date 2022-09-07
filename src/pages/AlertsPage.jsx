/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import {Routes, Route, useNavigate} from 'react-router-dom';
import Table, { AvatarCell, SelectColumnFilter, StatusPill } from './Table';

const AlertsPage = () => {
    const navigate = useNavigate();

    const url = wpApiSettings.root + 'tradingview-alerts/v1/alerts';
    const [alerts, setAlerts] = React.useState([]);

    React.useEffect(() => {
        async function loadAlerts() {
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
    
            const jobs = await response.json();
            setAlerts(jobs);
        }
    
        loadAlerts();
    }, []);

    const columns = React.useMemo(() => [
        {
          Header: "Name",
          accessor: 'name',
        },
        {
          Header: "Ticker",
          accessor: 'ticker',
        },
        {
          Header: "Type",
          accessor: 'type',
          Filter: SelectColumnFilter,
          filter: 'includes',
        },

        {
          Header: "Close",
          accessor: 'close',
        },
        {
          Header: "Time",
          accessor: 'time',
        },
      ], []);

    return (
        
        <div className="dashboard">
            <div className="min-h-screen bg-gray-100 text-gray-900">
                <main className="mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    <div className="">
                    <h3 className="text-xl font-semibold">Alerts</h3>
                    </div>

                    <div className="mt-6">
                        <Table columns={columns} data={jobs} />
                    </div>
                </main>
            </div>
            
        </div>
    );
};

export default AlertsPage;
