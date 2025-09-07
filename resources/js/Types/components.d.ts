// Type declarations for custom components
declare module '@/Components/Card' {
    import React from 'react';
    
    interface CardProps {
        children: React.ReactNode;
        className?: string;
        title?: string;
    }
    
    const Card: React.FC<CardProps>;
    export default Card;
}

declare module '@/Components/Button' {
    import React from 'react';
    
    interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
        variant?: 'primary' | 'secondary' | 'outline' | 'danger';
        size?: 'sm' | 'md' | 'lg';
        children: React.ReactNode;
        className?: string;
    }
    
    const Button: React.FC<ButtonProps>;
    export default Button;
}

declare module '@/Components/StatsCard' {
    import React from 'react';
    
    interface StatsCardProps {
        title: string;
        value: string;
        icon: React.ComponentType<{ className?: string }>;
        trend?: 'up' | 'down' | 'neutral';
        className?: string;
    }
    
    const StatsCard: React.FC<StatsCardProps>;
    export default StatsCard;
}

declare module '@/Components/TenantSelector' {
    import React from 'react';
    
    interface TenantSelectorProps {
        className?: string;
    }
    
    const TenantSelector: React.FC<TenantSelectorProps>;
    export default TenantSelector;
}
