import { createContext, useContext } from 'react';

export type NotificationType = 'success' | 'error';

export interface Notification {
  id: number;
  message: string;
  type: NotificationType;
}

export interface NotificationContextValue {
  notify: (message: string, type?: NotificationType) => void;
}

export const NotificationContext = createContext<NotificationContextValue | undefined>(undefined);

export const useNotify = () => {
  const context = useContext(NotificationContext);
  if (!context) {
    throw new Error('useNotify must be used within a NotificationProvider');
  }
  return context;
};
