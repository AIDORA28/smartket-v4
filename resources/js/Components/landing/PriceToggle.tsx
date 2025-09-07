import React from 'react';

interface PriceToggleProps {
  billingCycle: 'mensual' | 'anual';
  onToggle: (cycle: 'mensual' | 'anual') => void;
}

export default function PriceToggle({ billingCycle, onToggle }: PriceToggleProps) {
  const handleToggle = () => {
    onToggle(billingCycle === 'mensual' ? 'anual' : 'mensual');
  };

  return (
    <div className="flex items-center justify-center mb-12">
      <span className={`mr-3 ${billingCycle === 'mensual' ? 'text-gray-900 font-semibold' : 'text-gray-500'}`}>
        Mensual
      </span>
      <button
        onClick={handleToggle}
        className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
          billingCycle === 'anual' ? 'bg-red-600' : 'bg-gray-300'
        }`}
      >
        <span
          className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
            billingCycle === 'anual' ? 'translate-x-6' : 'translate-x-1'
          }`}
        />
      </button>
      <span className={`ml-3 ${billingCycle === 'anual' ? 'text-gray-900 font-semibold' : 'text-gray-500'}`}>
        Anual
      </span>
      {billingCycle === 'anual' && (
        <span className="ml-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
          Ahorra 20%
        </span>
      )}
    </div>
  );
}
