import { useState, useCallback, memo } from 'react';
import type { ImageGalleryProps } from '../../types';
import fallbackImage from '../../assets/fallbackImage.jpg';
import './ImageGallery.css';


export default memo(function ImageGallery({ images, productName }: ImageGalleryProps) {
    const [currentIndex, setCurrentIndex] = useState(0);

    const handlePrev = useCallback((e: React.MouseEvent) => {
        e.stopPropagation();
        setCurrentIndex(prev => (prev > 0 ? prev - 1 : images.length - 1));
    }, [images.length]);

    const handleNext = useCallback((e: React.MouseEvent) => {
        e.stopPropagation();
        setCurrentIndex(prev => (prev < images.length - 1 ? prev + 1 : 0));
    }, [images.length]);

    return (
        <div className="product-details__gallery" data-testid="product-gallery">
            <div className="product-details__thumbnails">
                {images.map((img, i) => (
                    <button
                        key={i}
                        className={`product-details__thumb-btn ${currentIndex === i ? 'product-details__thumb-btn--active' : ''}`}
                        onClick={() => setCurrentIndex(i)}
                    >
                        <img
                            src={img}
                            alt={`${productName} thumbnail ${i + 1}`}
                            className="product-details__thumb-img"
                            loading="lazy"
                            decoding="async"
                            width={100}
                            height={100}
                            onError={(e) => {
                                e.currentTarget.src = fallbackImage;
                            }}
                        />
                    </button>
                ))}
            </div>
            <div className="product-details__main-image-wrapper">
                {images.length > 1 && (
                    <button
                        className="product-details__arrow product-details__arrow--left"
                        onClick={handlePrev}
                        aria-label="Previous image"
                    >
                        &#10094;
                    </button>
                )}
                <img
                    src={images[currentIndex]}
                    alt={productName}
                    className="product-details__main-image"
                    fetchPriority="high"
                    loading="eager"
                    width="700"
                    height="700"
                    onError={(e) => {
                        e.currentTarget.src = fallbackImage;
                    }}
                />

                {images.length > 1 && (
                    <button
                        className="product-details__arrow product-details__arrow--right"
                        onClick={handleNext}
                        aria-label="Next image"
                    >
                        &#10095;
                    </button>
                )}
            </div>
        </div>
    )
});