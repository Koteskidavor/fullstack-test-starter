import type { StatusMessageProps } from '../../types/index';
import './StatusMessage.css';

const StatusMessage = ({ message }: StatusMessageProps) => {
    return (
        <div className="status-message" role="status" aria-live="polite">
            {message}
        </div>
    );
};

export default StatusMessage;
