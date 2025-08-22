import { BrowserRouter } from 'react-router-dom';

import Header from '@Components/common/Header';
import Routes from '@Admin/Routes';

const ViewContainer = () => {
	return (
		<BrowserRouter>
			<Header />
			<Routes />
		</BrowserRouter>
	);
};

export default ViewContainer;
