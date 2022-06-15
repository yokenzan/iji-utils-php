<?php

declare(strict_types=1);

namespace IjiUtils\MedicalFee\Amount\Burden\Contracts;

use IjiUtils\MedicalFee\ValueObjects\Point;
use IjiUtils\MedicalInsurance\ValueObjects\Amount;

interface BurdenBreakdownInterface
{
    /* 助成の有無 */

    /**
     * 負担割合の助成をもつか？
     */
    public function hasSubsidyByRate(): bool;

    /**
     * 上限額の助成をもつか？
     */
    public function hasSubsidyByLimit(): bool;

    /* 実際に助成が適用されたか */

    /**
     * 負担割合の助成による給付が発生したか？
     */
    public function providesByRate(): bool;

    /**
     * 上限額の助成による給付が発生したか？
     */
    public function providesByLimit(): bool;

    /* 按分比率 */

    /**
     * 負担割合を実数で返します
     */
    public function getBurdenRate(): float;

    // public function getLimitAmount(): ?Amount;

    /* 実際の給付・負担 */

    /**
     * この制度の負担割合の助成による負担金額を返します。
     */
    public function getBurdenAmountByRate(): ?Amount;

    /**
     * この制度の上限額の助成による負担金額を返します。
     */
    public function getBurdenAmountByLimit(): ?Amount;

    /**
     * この制度による最終的な患者の窓口一部負担金を返します
     */
    public function getBurdenAmount(): ?Amount;

    /* 他 */

    /**
     * 請求点数を返します
     */
    public function getPoint(): Point;

    /**
     * 給付対象額を返します
     */
    public function getTargetAmount(): Amount;

    /**
     * 負担割合による負担金と上限額による負担金の差額を返します
     */
    public function getDiffBetweenRateAndLimit(): ?Amount;
}
