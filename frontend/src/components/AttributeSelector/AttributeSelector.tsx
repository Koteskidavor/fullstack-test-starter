import { kebabCase } from "../../utils/kebabCase";
import type { AttributeProps } from "../../types";
import './AttributeSelector.css';

export default function AttributeSelector({ attributes, selectedAttributes, onChange }: AttributeProps) {
    return (
        <>
            {attributes.map((attr) => (
                <div key={attr.id} className="product-details__attr-group" data-testid={`product-attribute-${kebabCase(attr.name)}`}>
                    <p className="product-details__attr-label">
                        {attr.name.toUpperCase()}:
                    </p>
                    <div className="product-details__attr-options">
                        {attr.items.map((item) => {
                            const isSelected = selectedAttributes[attr.id] === item.id;
                            if (attr.type === 'swatch') {
                                return (
                                    <button
                                        key={item.id}
                                        className={`product-details__swatch-btn ${isSelected ? 'product-details__swatch-btn--selected' : ''}`}
                                        data-testid={`product-attribute-${kebabCase(attr.name)}-${kebabCase(item.id)}${isSelected ? '-selected' : ''}`}
                                        style={{ backgroundColor: item.value }}
                                        onClick={() => onChange(attr.id, item.id)}
                                        title={item.displayValue}
                                    />
                                );
                            }
                            return (
                                <button
                                    key={item.id}
                                    className={`product-details__text-btn ${isSelected ? 'product-details__text-btn--selected' : ''}`}
                                    data-testid={`product-attribute-${kebabCase(attr.name)}-${kebabCase(item.id)}${isSelected ? '-selected' : ''}`}
                                    onClick={() => onChange(attr.id, item.id)}
                                >
                                    {item.value}
                                </button>
                            );
                        })}
                    </div>
                </div>
            ))}
        </>
    )
}