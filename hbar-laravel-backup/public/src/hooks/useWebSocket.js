import { useEffect, useRef } from 'react';
import { io } from 'socket.io-client';
import { useStore } from '../store';

export default function useWebSocket() {
  const socketRef = useRef(null);
  const { setPriceData, setConnectionStatus } = useStore();

  useEffect(() => {
    if (socketRef.current) {
      socketRef.current.disconnect();
    }

    socketRef.current = io('http://localhost:3001', {
      transports: ['websocket', 'polling'],
      reconnection: true,
      reconnectionAttempts: 5,
      reconnectionDelay: 2000,
    });

    const socket = socketRef.current;

    socket.on('connect', () => {
      console.log('WebSocket connected');
      setConnectionStatus('connected');
    });

    socket.on('disconnect', () => {
      console.log('WebSocket disconnected');
      setConnectionStatus('disconnected');
    });

    socket.on('connect_error', (err) => {
      console.error('WebSocket connection error:', err.message);
      setConnectionStatus('error');
    });

    socket.on('realtime-update', (data) => {
      setPriceData(data);
    });

    return () => {
      if (socket) socket.disconnect();
      socketRef.current = null;
    };
  }, [setPriceData, setConnectionStatus]);
}
