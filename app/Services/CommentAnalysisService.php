<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Product;

class CommentAnalysisService
{
    public function analyzeProduct(Product $product): void
    {
        $comments = Comment::where('commentable_type', Product::class)
            ->where('commentable_id', $product->id)
            ->whereNull('parent_id')
            ->pluck('comment');

        $positiveWords = [
            'great',
            'nice',
            'good',
            'amazing',
            'excellent',
            'perfect',
            'awesome',
            'رائع',
            'رااائع',
            'ممتاز',
            'جميل',
            'حلو',
            'مبدع',
            'عجبني',
            'فخم',
            'تحفة'
        ];

        $negativeWords = [
            'bad',
            'poor',
            'terrible',
            'awful',
            'hate',
            'worst',
            'سيء',
            'سيئ',
            'غالي',
            'رديء',
            'مش حلو',
            'ما عجبني',
            'ضعيف'
        ];

        $positive = 0;
        $negative = 0;
        $neutral = 0;

        foreach ($comments as $comment) {

            $text = mb_strtolower($comment);

            $isPositive = false;
            $isNegative = false;

            foreach ($positiveWords as $word) {
                if (str_contains($text, mb_strtolower($word))) {
                    $isPositive = true;
                    break;
                }
            }

            foreach ($negativeWords as $word) {
                if (str_contains($text, mb_strtolower($word))) {
                    $isNegative = true;
                    break;
                }
            }

            if ($isPositive && !$isNegative) {
                $positive++;
            } elseif ($isNegative && !$isPositive) {
                $negative++;
            } else {
                $neutral++;
            }
        }

        $total = $positive + $negative + $neutral;

        $score = 0;

        if ($total > 0) {
            $score = round(($positive / $total) * 100);
        }

        $label = 'neutral';

        if ($score >= 70) {
            $label = 'positive';
        } elseif ($score <= 40) {
            $label = 'negative';
        }

        $product->update([
            'ai_score' => $score,
            'positive_comments' => $positive,
            'neutral_comments' => $neutral,
            'negative_comments' => $negative,
            'sentiment_label' => $label,
        ]);
    }
}