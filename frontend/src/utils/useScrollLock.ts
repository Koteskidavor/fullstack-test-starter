import { useEffect, useRef } from 'react';


export const useScrollLock = (isLocked: boolean) => {
  const lockCountRef = useRef(0);
  useEffect(() => {
    if (!isLocked) return;

    lockCountRef.current++;

    if (lockCountRef.current === 1) {
      const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
      document.body.style.overflow = 'hidden';
      if (scrollBarWidth > 0) {
        document.body.style.paddingRight = `${scrollBarWidth}px`;
      }
    }

    return () => {
      lockCountRef.current--;
      if (lockCountRef.current === 0) {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
      }
    };
  }, [isLocked]);
};
