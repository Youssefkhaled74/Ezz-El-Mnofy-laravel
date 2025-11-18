<?php

namespace App\Exports;

use App\Models\OrderRating;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrderRatingsExport implements WithMultipleSheets
{
    protected $averages;
    protected $positivePercentages;
    protected $ratings;

    public function __construct($averages, $positivePercentages, $ratings)
    {
        $this->averages = $averages;
        $this->positivePercentages = $positivePercentages;
        $this->ratings = $ratings;
    }

    public function sheets(): array
    {
        return [
            new StatisticsSheet($this->averages, $this->positivePercentages),
            new RatingsSheet($this->ratings),
        ];
    }
}

class StatisticsSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $averages;
    protected $positivePercentages;

    public function __construct($averages, $positivePercentages)
    {
        $this->averages = $averages;
        $this->positivePercentages = $positivePercentages;
    }

    public function collection()
    {
        return new Collection([
            [
                'Delivery Time' => number_format($this->averages?->avg_delivery_time, 2),
                'Food Quality' => number_format($this->averages?->avg_food_quality, 2),
                'Overall Experience' => number_format($this->averages?->avg_overall_experience, 2),
                'Packing' => number_format($this->averages?->avg_packing, 2),
                'Delivery Service' => number_format($this->averages?->avg_delivery_service, 2),
                'Overall Average' => number_format($this->averages?->avg_overall_rating, 2),
                'Total Ratings' => $this->averages?->total_ratings,
            ],
            [
                'Delivery Time (% Rated 4+)' => number_format($this->positivePercentages?->positive_delivery_time, 1) . '%',
                'Food Quality (% Rated 4+)' => number_format($this->positivePercentages?->positive_food_quality, 1) . '%',
                'Overall Experience (% Rated 4+)' => number_format($this->positivePercentages?->positive_overall_experience, 1) . '%',
                'Packing (% Rated 4+)' => number_format($this->positivePercentages?->positive_packing, 1) . '%',
                'Delivery Service (% Rated 4+)' => number_format($this->positivePercentages?->positive_delivery_service, 1) . '%',
                'Overall Average (% Rated 4+)' => '',
                'Total Ratings' => '',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Delivery Time',
            'Food Quality',
            'Overall Experience',
            'Packing',
            'Delivery Service',
            'Overall Average',
            'Total Ratings',
        ];
    }

    public function title(): string
    {
        return 'Statistics';
    }
}

class RatingsSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    protected $ratings;

    public function __construct($ratings)
    {
        $this->ratings = $ratings;
    }

    public function collection()
    {
        return $this->ratings;
    }

    public function headings(): array
    {
        return [
            'User',
            'Order ID',
            'Order Type',
            'Delivery Time',
            'Food Quality',
            'Overall Experience',
            'Packing',
            'Delivery Service',
            'Comment',
            'Photo',
        ];
    }

    public function map($rating): array
    {
        $orderType = match ($rating->order->order_type ?? null) {
            \App\Enums\OrderType::DELIVERY => 'Delivery',
            \App\Enums\OrderType::TAKEAWAY => 'Takeaway',
            default => 'N/A',
        };

        return [
            $rating->user->name ?? 'No Name',
            $rating->order_id,
            $orderType,
            $rating->delivery_time,
            $rating->food_quality,
            $rating->overall_experience,
            $rating->packing,
            $rating->delivery_service,
            $rating->additional_note,
            $rating->hasMedia('rating_photo') ? $rating->getFirstMediaUrl('rating_photo') : 'N/A',
        ];
    }


    public function title(): string
    {
        return 'Ratings';
    }
}
