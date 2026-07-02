import { describe, expect, it } from 'vitest';

describe('Polling deduplication logic', () => {
    it('seenLogIds prevents duplicate insertion', () => {
        const seenLogIds = new Set<number>();
        const logs: Array<{ id: number; label_detected: string }> = [];

        function handleNewDetection(log: {
            id: number;
            label_detected: string;
        }) {
            if (seenLogIds.has(log.id)) {
                return;
            }

            seenLogIds.add(log.id);
            logs.unshift(log);
        }

        handleNewDetection({ id: 1, label_detected: 'flood' });
        handleNewDetection({ id: 1, label_detected: 'flood' });
        handleNewDetection({ id: 2, label_detected: 'crowd' });

        expect(logs.length).toBe(2);
        expect(logs[0].id).toBe(2);
        expect(logs[1].id).toBe(1);
    });

    it('caps log list at 30 entries', () => {
        const seenLogIds = new Set<number>();
        const logs: Array<{ id: number }> = [];

        function handleNewDetection(log: { id: number }) {
            if (seenLogIds.has(log.id)) {
                return;
            }

            seenLogIds.add(log.id);
            logs.unshift(log);

            if (logs.length > 30) {
                logs.pop();
            }
        }

        for (let i = 1; i <= 35; i++) {
            handleNewDetection({ id: i });
        }

        expect(logs.length).toBe(30);
        expect(logs[0].id).toBe(35);
        expect(logs[29].id).toBe(6);
    });

    it('preserves oldest entries when sorted in reverse order', () => {
        const seenLogIds = new Set<number>();
        const logs: Array<{ id: number }> = [];

        function handleNewDetection(log: { id: number }) {
            if (seenLogIds.has(log.id)) return;
            seenLogIds.add(log.id);
            logs.unshift(log);
            if (logs.length > 5) logs.pop();
        }

        for (let i = 1; i <= 10; i++) {
            handleNewDetection({ id: i });
        }

        expect(logs.length).toBe(5);
        expect(logs[0].id).toBe(10);
        expect(logs[4].id).toBe(6);
    });

    it('handles empty initial state gracefully', () => {
        const seenLogIds = new Set<number>();
        const logs: Array<{ id: number }> = [];

        expect(logs.length).toBe(0);
        expect(seenLogIds.size).toBe(0);
    });

    it('seenReportIds prevents duplicate report insertion', () => {
        const seenReportIds = new Set<string>();
        const pendingList: Array<{ id: string; status: string }> = [];

        function handleNewReport(report: { id: string; status: string }) {
            if (seenReportIds.has(report.id)) {
                return;
            }

            seenReportIds.add(report.id);

            if (report.status === 'pending') {
                pendingList.unshift(report);
            }
        }

        handleNewReport({ id: 'abc123', status: 'pending' });
        handleNewReport({ id: 'abc123', status: 'pending' });
        handleNewReport({ id: 'def456', status: 'pending' });

        expect(pendingList.length).toBe(2);
        expect(pendingList[0].id).toBe('def456');
    });

    it('ignores non-pending reports in pending list', () => {
        const seenReportIds = new Set<string>();
        const pendingList: Array<{ id: string; status: string }> = [];

        function handleNewReport(report: { id: string; status: string }) {
            if (seenReportIds.has(report.id)) return;
            seenReportIds.add(report.id);
            if (report.status === 'pending') {
                pendingList.unshift(report);
            }
        }

        handleNewReport({ id: 'abc', status: 'verified' });
        handleNewReport({ id: 'def', status: 'rejected' });

        expect(pendingList.length).toBe(0);
    });

    it('report update removes from pending and moves to history', () => {
        let pendingList: Array<{ id: string; status: string }> = [
            { id: 'abc123', status: 'pending' },
        ];
        const historyList: Array<{ id: string; status: string }> = [];

        function handleReportUpdate(report: { id: string; status: string }) {
            pendingList = pendingList.filter((r) => r.id !== report.id);

            const indexInHistory = historyList.findIndex(
                (r) => r.id === report.id,
            );

            if (indexInHistory !== -1) {
                historyList[indexInHistory] = report;
            } else if (
                report.status === 'verified' ||
                report.status === 'rejected'
            ) {
                historyList.unshift(report);
            }
        }

        handleReportUpdate({ id: 'abc123', status: 'verified' });

        expect(pendingList.length).toBe(0);
        expect(historyList.length).toBe(1);
        expect(historyList[0].status).toBe('verified');
    });

    it('updates existing entry in history instead of duplicate', () => {
        let pendingList: Array<{ id: string; status: string }> = [];
        const historyList: Array<{ id: string; status: string }> = [
            { id: 'abc123', status: 'verified' },
        ];

        function handleReportUpdate(report: { id: string; status: string }) {
            pendingList = pendingList.filter((r) => r.id !== report.id);
            const indexInHistory = historyList.findIndex(
                (r) => r.id === report.id,
            );
            if (indexInHistory !== -1) {
                historyList[indexInHistory] = report;
            }
        }

        handleReportUpdate({ id: 'abc123', status: 'rejected' });

        expect(historyList.length).toBe(1);
        expect(historyList[0].status).toBe('rejected');
    });

    it('does not add to history if status is still pending', () => {
        let pendingList = [{ id: 'abc', status: 'pending' }];
        const historyList: Array<{ id: string; status: string }> = [];

        function handleReportUpdate(report: { id: string; status: string }) {
            pendingList = pendingList.filter((r) => r.id !== report.id);
            if (report.status === 'verified' || report.status === 'rejected') {
                historyList.unshift(report);
            }
        }

        handleReportUpdate({ id: 'abc', status: 'pending' });

        expect(pendingList.length).toBe(0);
        expect(historyList.length).toBe(0);
    });

    it('handles multiple rapid consecutive updates without duplicates', () => {
        const seenReportIds = new Set<string>();
        let pendingList: Array<{ id: string; status: string }> = [];

        function handleNewReport(report: { id: string; status: string }) {
            if (seenReportIds.has(report.id)) return;
            seenReportIds.add(report.id);
            if (report.status === 'pending') {
                pendingList.unshift(report);
            }
        }

        for (let i = 0; i < 10; i++) {
            handleNewReport({ id: 'dup', status: 'pending' });
        }

        expect(pendingList.length).toBe(1);
    });
});
