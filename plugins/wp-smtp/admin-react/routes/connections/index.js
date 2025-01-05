/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { STORE_NAME } from '../../data/src/connections/constants';
import EmptyConnections from './empty-connections';
import ConnectionsList from './connections-list';
import { Container } from './styles';

function Connections() {
	const connections = useSelect(
		( select ) => select( STORE_NAME ).getConnections(),
		[]
	);
	return (
		<Container>
			{ connections.length === 0 ? (
				<EmptyConnections />
			) : (
				<ConnectionsList connections={ connections } />
			) }
		</Container>
	);
}

export default Connections;
