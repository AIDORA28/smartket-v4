import React, { InputHTMLAttributes } from 'react';

interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
  label: string;
  error?: string;
  icon?: React.ReactNode;
}

export default function Input({ 
  label, 
  error, 
  icon, 
  className = '', 
  ...props 
}: InputProps) {
  return (
    <div className="space-y-1">
      <label 
        htmlFor={props.id} 
        className="block text-sm font-semibold text-gray-700"
      >
        {label}
      </label>
      
      <div className="relative">
        {icon && (
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
            {icon}
          </div>
        )}
        
        <input
          {...props}
          className={`
            w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm
            focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500
            transition-all duration-300
            ${icon ? 'pl-10' : ''}
            ${error ? 'border-red-300 focus:ring-red-500' : 'border-gray-300'}
            ${className}
          `}
        />
      </div>
      
      {error && (
        <p className="text-sm text-red-600 mt-1">
          {error}
        </p>
      )}
    </div>
  );
}
