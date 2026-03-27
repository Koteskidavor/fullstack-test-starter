import { useState, useCallback, useRef, useEffect } from 'react';
import type { ReactNode } from 'react';
import { NotificationContext, type Notification, type NotificationType } from './context/NotificationContext';
import './Notification.css';

export const NotificationProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const timerRef = useRef<ReturnType<typeof setTimeout>[]>([]);
  const idCounterRef = useRef(0);

  const notify = useCallback((message: string, type: NotificationType = 'success') => {
    const id = ++idCounterRef.current;
    setNotifications((prev) => [...prev, { id, message, type }]);

    const timer = setTimeout(() => {
      setNotifications((prev) => prev.filter((n) => n.id !== id));

      timerRef.current = timerRef.current.filter((t) => t !== timer);
    }, 4000);

    timerRef.current.push(timer);
  }, []);

  useEffect(() => {
    return () => {
      timerRef.current.forEach((timer) => clearTimeout(timer));
    };
  }, []);

  return (
    <NotificationContext.Provider value={{ notify }}>
      {children}
      <div className="notification-container">
        {notifications.map((n) => (
          <div key={n.id} className={`notification notification--${n.type}`}>
            <span className="notification__message">{n.message}</span>
          </div>
        ))}
      </div>
    </NotificationContext.Provider>
  );
};