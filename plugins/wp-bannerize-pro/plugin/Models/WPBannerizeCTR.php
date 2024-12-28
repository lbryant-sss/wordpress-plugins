<?php

namespace WPBannerize\Models;

class WPBannerizeCTR
{

    public static function getTrends($args = [])
    {
        global $wpdb;

        $clicks_table = WPBannerizeClicks::getTable();
        $impressions_table = WPBannerizeImpressions::getTable();

        [
            'accuracy' => $accuracy,
            'campaigns' => $campaigns,
        ] = $args;

        $where = 'WHERE 1';

        if ($campaigns) {
            $where .= " AND (tt.term_id IN ($campaigns)) AND (c.ID IS NULL OR tt2.term_id IN ($campaigns))";
        }

        $date_format = [
            'day' => '%%Y-%%m-%%d',
            'month' => '%%Y-%%m',
            'year' => '%%Y',
        ][$accuracy];


        return $wpdb->get_results($wpdb->prepare(
            "SELECT
          DATE_FORMAT(i.date, '$date_format') AS date,
          COUNT(DISTINCT i.ID) AS impressions_count,
          COUNT(DISTINCT c.ID) AS clicks_count,
          ROUND((COUNT(DISTINCT c.ID) / COUNT(DISTINCT i.ID)) * 100, 2) AS ctr
      FROM
          %i i
      LEFT JOIN
          %i c ON i.banner_id = c.banner_id AND DATE_FORMAT(i.date, '$date_format') = DATE_FORMAT(c.date, '$date_format')
      JOIN
          $wpdb->term_relationships tr ON i.banner_id = tr.object_id
      JOIN
          $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'wp_bannerize_tax'
      LEFT JOIN
          $wpdb->term_relationships tr2 ON c.banner_id = tr2.object_id
      LEFT JOIN
          $wpdb->term_taxonomy tt2 ON tr2.term_taxonomy_id = tt2.term_taxonomy_id AND tt2.taxonomy = 'wp_bannerize_tax'
      %1s
      GROUP BY
          date
      HAVING
          impressions_count > 0
      ORDER BY
          date",
            $impressions_table,
            $clicks_table,
            $where
        ), ARRAY_A);
    }
}
