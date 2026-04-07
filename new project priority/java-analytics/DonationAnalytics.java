package analytics;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

/**
 * Smart NGO Donation Analytics Module
 * Purpose: Standalone reporting tool for impact data.
 */
public class DonationAnalytics {

    private static final String URL = "jdbc:mysql://localhost:3306/smart_ngo_db";
    private static final String USER = "root";
    private static final String PASS = "";

    public static void main(String[] args) {
        System.out.println("==========================================");
        System.out.println("   Smart NGO Platform - Analytics Module  ");
        System.out.println("==========================================\n");

        try (Connection conn = DriverManager.getConnection(URL, USER, PASS)) {
            generateDonationSummary(conn);
            generateProjectPerformance(conn);
            generateNGOVerificationStats(conn);
        } catch (SQLException e) {
            System.err.println("Database connection failed: " + e.getMessage());
            System.out.println("NOTE: Ensure MySQL is running and the database is configured.");
        }
    }

    private static void generateDonationSummary(Connection conn) throws SQLException {
        String sql = "SELECT SUM(amount) as total, COUNT(*) as count FROM donations";
        try (Statement stmt = conn.createStatement(); ResultSet rs = stmt.executeQuery(sql)) {
            if (rs.next()) {
                System.out.println("[DONATION SUMMARY]");
                System.out.printf("Total Donations: $%.2f%n", rs.getDouble("total"));
                System.out.println("Transactions Processed: " + rs.getInt("count"));
                System.out.println();
            }
        }
    }

    private static void generateProjectPerformance(Connection conn) throws SQLException {
        String sql = "SELECT title, goal_amount, collected_amount, (collected_amount/goal_amount)*100 as pct " +
                     "FROM projects ORDER BY pct DESC LIMIT 5";
        System.out.println("[TOP PERFORMING PROJECTS]");
        try (Statement stmt = conn.createStatement(); ResultSet rs = stmt.executeQuery(sql)) {
            while (rs.next()) {
                System.out.printf("- %s: $%.2f / $%.2f (%.1f%%)%n", 
                                  rs.getString("title"), 
                                  rs.getDouble("collected_amount"), 
                                  rs.getDouble("goal_amount"), 
                                  rs.getDouble("pct"));
            }
            System.out.println();
        }
    }

    private static void generateNGOVerificationStats(Connection conn) throws SQLException {
        String sql = "SELECT verification_status, COUNT(*) as count FROM ngos GROUP BY verification_status";
        System.out.println("[NGO VERIFICATION STATUS]");
        try (Statement stmt = conn.createStatement(); ResultSet rs = stmt.executeQuery(sql)) {
            while (rs.next()) {
                System.out.printf("- %s: %d NGOs%n", 
                                  rs.getString("verification_status").toUpperCase(), 
                                  rs.getInt("count"));
            }
            System.out.println();
        }
    }
}
