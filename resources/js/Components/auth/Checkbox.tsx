import React, { InputHTMLAttributes } from 'react';

interface CheckboxProps extends InputHTMLAttributes<HTMLInputElement> {
  label: string;
}

export default function Checkbox({ label, className = '', ...props }: CheckboxProps) {
  return (
    <label className="flex items-center cursor-pointer group">
      <div className="relative">
        <input
          type="checkbox"
          {...props}
          className={`
            h-5 w-5 rounded border-2 border-gray-300 text-red-600 
            focus:ring-2 focus:ring-red-500 focus:ring-offset-0
            transition-all duration-200
            group-hover:border-red-400
            ${className}
          `}
        />
      </div>
      <span className="ml-3 text-sm text-gray-700 group-hover:text-gray-900 transition-colors">
        {label}
      </span>
    </label>
  );
}
