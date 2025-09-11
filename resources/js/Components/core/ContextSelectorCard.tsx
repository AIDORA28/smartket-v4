import React from 'react';
import MetricCard from './shared/MetricCard';
import { BuildingOfficeIcon, MapPinIcon } from '@heroicons/react/24/outline';

interface ContextSelectorCardProps {
  title: string;
  currentItem: any;
  emoji: string;
  planInfo?: string;
  onClick?: () => void;
}

const ContextSelectorCard: React.FC<ContextSelectorCardProps> = ({
  title,
  currentItem,
  emoji,
  planInfo,
  onClick
}) => {
  return (
    <MetricCard
      title={title}
      value={currentItem?.nombre || 'No seleccionado'}
      emoji={emoji}
      subtitle={planInfo}
      color="blue"
    />
  );
};

export default ContextSelectorCard;
